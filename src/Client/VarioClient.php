<?php

declare(strict_types=1);

namespace Lemonade\Vario\Client;

use Closure;
use Lemonade\Vario\Auth\Storage\TokenStorageInterface;
use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Exception\ApiException;
use Lemonade\Vario\Exception\AuthenticationException;
use Lemonade\Vario\Exception\ForbiddenException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class VarioClient
 *
 * Low-level HTTP client responsible for communicating with the Vario Online API.
 *
 * This client acts as the transport layer for the SDK and is used internally
 * by higher level API modules (DatasetViewApi, KnownPartyApi, etc.).
 *
 * Responsibilities of this class include:
 *
 *  - building and sending PSR-7 HTTP requests
 *  - attaching authentication tokens to outgoing requests
 *  - automatic retry after token expiration (401 responses)
 *  - structured logging of requests and responses
 *  - decoding JSON responses returned by the API
 *
 * The implementation is fully based on PSR standards:
 *
 *  - PSR-7  HTTP messages
 *  - PSR-17 HTTP factories
 *  - PSR-18 HTTP client
 *  - PSR-3  logging
 *
 * This class intentionally stays transport-focused and does not contain
 * any domain logic. Domain mapping is handled by higher-level API layers.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Client
 * @category    Client
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class VarioClient implements VarioClientInterface
{
    private readonly Closure $reauthCallback;

    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly LoggerInterface $logger,
        callable $reauthCallback
    ) {
        // normalize callable → Closure (PHP 8.1 safe storage)
        $this->reauthCallback = Closure::fromCallable($reauthCallback);
    }

    /**
     * Send raw request (with retry + auth handling).
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        return $this->sendWithRetry($request, true);
    }

    /**
     * @param array<string,mixed>|list<mixed>|null $payload
     * @return array<string,mixed>|list<mixed>
     */
    public function sendJson(
        HttpMethod $method,
        string $uri,
        ?array $payload = null
    ): array {
        return $this->sendAndDecode(
            $method,
            $uri,
            static function (RequestInterface $request) use ($payload): RequestInterface {
                $request = $request->withHeader(
                    'Content-Type',
                    'application/json'
                );

                if ($payload !== null) {
                    $request->getBody()->write(
                        json_encode($payload, JSON_THROW_ON_ERROR)
                    );
                }

                return $request;
            }
        );
    }

    /**
     * @param array<string,mixed> $query
     * @return array<string,mixed>
     */
    public function sendQuery(
        HttpMethod $method,
        string $uri,
        array $query
    ): array {
        /** @var array<string,mixed> $result */
        $result = $this->sendAndDecode(
            $method,
            $uri,
            static function (RequestInterface $request) use ($query): RequestInterface {
                $uri = $request->getUri()
                    ->withQuery(http_build_query($query, '', '&', PHP_QUERY_RFC3986));

                return $request->withUri($uri);
            }
        );

        return $result;
    }

    /**
     * @return array<string,mixed>|list<mixed>
     */
    private function decodeJsonResponse(ResponseInterface $response): array
    {
        $body = (string) $response->getBody();

        if ($body === '') {
            return [];
        }

        $result = json_decode(
            $body,
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (!is_array($result)) {
            throw new ApiException(sprintf(
                'Invalid JSON response from Vario API (%s). Expected JSON object or array.',
                $response->getStatusCode()
            ));
        }

        /** @var array<string,mixed>|list<mixed> $result */
        return $result;
    }

    /**
     * @param callable(RequestInterface): RequestInterface $requestBuilder
     * @return array<string,mixed>|list<mixed>
     */
    private function sendAndDecode(
        HttpMethod $method,
        string $uri,
        callable $requestBuilder
    ): array {
        $request = $this->requestFactory
            ->createRequest($method->value, $uri);

        $request = $requestBuilder($request);

        $response = $this->send($request);

        return $this->decodeJsonResponse($response);
    }

    private function sendWithRetry(
        RequestInterface $request,
        bool $allowRetry
    ): ResponseInterface {

        $request = $this->prepareRequest($request);

        $uri = $request->getUri();

        $this->logger->debug('Vario API request', [
            'method' => $request->getMethod(),
            'uri' => $uri->getPath(),
            'query' => $uri->getQuery(),
            'authorized' => $request->hasHeader('Authorization'),
            'content_length' => $request->getBody()->getSize(),
        ]);

        $start = microtime(true);

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $e) {

            $duration = (microtime(true) - $start) * 1000;

            $this->logger->error('Vario API HTTP request failed', [
                'uri' => (string) $request->getUri(),
                'duration_ms' => round($duration, 2),
                'exception' => $e,
            ]);

            throw new ApiException(
                'HTTP request to Vario API failed',
                previous: $e
            );
        }

        $duration = (microtime(true) - $start) * 1000;

        $status = $response->getStatusCode();
        $uri = $request->getUri();

        $this->logger->debug('Vario API response', [
            'status' => $status,
            'uri' => $uri->getPath(),
            'query' => $uri->getQuery(),
            'content_length' => $response->getBody()->getSize(),
            'duration_ms' => round($duration, 2),
        ]);

        // 401 handling
        if ($status === 401) {

            $this->logger->warning('Vario API unauthorized (401)', [
                'uri' => (string) $request->getUri(),
                'retry' => $allowRetry,
            ]);

            if ($allowRetry) {
                $this->reauthenticate();
                return $this->sendWithRetry($request, false);
            }

            throw new AuthenticationException(
                'Authentication failed after retry. Check login credentials or company number.'
            );
        }

        // permission problem
        if ($status === 403) {
            $body = (string) $response->getBody();


            $this->logger->warning('Vario API forbidden', [
                'uri' => (string) $request->getUri(),
            ]);

            throw new ForbiddenException(
                sprintf(
                    "Vario API forbidden\nURI: %s",
                    (string) $request->getUri()
                ),
                403,
                $body
            );
        }

        // generic API error
        if ($status >= 400) {
            $body = (string) $response->getBody();

            $this->logger->error('Vario API error', [
                'status' => $status,
                'uri' => (string) $request->getUri(),
            ]);

            throw new ApiException(
                sprintf(
                    "Vario API error\nStatus: %d\nURI: %s",
                    $status,
                    (string) $request->getUri()
                ),
                $status,
                $body
            );
        }

        return $response;
    }

    private function prepareRequest(RequestInterface $request): RequestInterface
    {
        $request = $request->withHeader(
            'X-Requested-With',
            'XMLHttpRequest'
        );

        if ($request->hasHeader('Authorization')) {
            $request = $request->withoutHeader('Authorization');
        }

        $token = $this->tokenStorage->get();

        if ($token === null) {
            return $request;
        }

        return $request->withHeader(
            'Authorization',
            'Bearer ' . $token->value
        );
    }

    private function reauthenticate(): void
    {
        $this->logger->info('Vario API re-authentication triggered');
        $this->logger->debug(
            'Vario auth token cleared before re-authentication'
        );
        $this->tokenStorage->clear();

        try {
            ($this->reauthCallback)();
        } catch (\Throwable $e) {

            $this->logger->error('Vario API re-authentication failed', [
                'exception' => $e,
            ]);

            throw new AuthenticationException(
                'Re-authentication failed',
                previous: $e
            );
        }
    }
}
