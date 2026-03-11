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
use Psr\Http\Message\StreamFactoryInterface;
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
        private readonly StreamFactoryInterface $streamFactory,
        private readonly LoggerInterface $logger,
        callable $reauthCallback
    ) {
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
     * @param array<string, mixed>|list<mixed>|null $payload
     * @return array<string, mixed>|list<mixed>
     */
    public function sendJson(
        HttpMethod $method,
        string $uri,
        ?array $payload = null
    ): array {
        $request = $this->createRequest($method, $uri);

        if ($payload !== null) {
            $request = $this->withJsonBody($request, $payload);
        }

        return $this->decodeJsonResponse(
            $this->send($request)
        );
    }

    /**
     * @param array<string, mixed> $query
     */
    public function sendQuery(
        HttpMethod $method,
        string $uri,
        array $query
    ): array {
        $request = $this->createRequest($method, $uri);
        $request = $this->withQuery($request, $query);

        return $this->decodeJsonResponse(
            $this->send($request)
        );
    }

    private function createRequest(HttpMethod $method, string $uri): RequestInterface
    {
        return $this->requestFactory->createRequest($method->value, $uri);
    }

    /**
     * @param array<string, mixed>|list<mixed> $payload
     */
    private function withJsonBody(
        RequestInterface $request,
        array $payload
    ): RequestInterface {
        $json = json_encode($payload, JSON_THROW_ON_ERROR);
        $stream = $this->streamFactory->createStream($json);

        return $request
            ->withHeader('Content-Type', 'application/json')
            ->withBody($stream);
    }

    /**
     * @param array<string, mixed> $query
     */
    private function withQuery(
        RequestInterface $request,
        array $query
    ): RequestInterface {
        $uri = $request->getUri()->withQuery(
            http_build_query($query, '', '&', PHP_QUERY_RFC3986)
        );

        return $request->withUri($uri);
    }

    /**
     * @return array<string, mixed>|list<mixed>
     */
    private function decodeJsonResponse(ResponseInterface $response): array
    {
        $body = (string) $response->getBody();

        if ($body === '') {
            return [];
        }

        $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($decoded)) {
            throw new ApiException(sprintf(
                'Invalid JSON response from Vario API (%d). Expected JSON object or array.',
                $response->getStatusCode()
            ));
        }

        /** @var array<string, mixed>|list<mixed> $decoded */
        return $decoded;
    }

    private function sendWithRetry(
        RequestInterface $request,
        bool $allowRetry
    ): ResponseInterface {
        $preparedRequest = $this->prepareRequest($request);
        $this->rewindRequestBody($preparedRequest);

        $this->logRequest($preparedRequest);

        $start = microtime(true);

        try {
            $response = $this->httpClient->sendRequest($preparedRequest);
        } catch (ClientExceptionInterface $e) {
            $durationMs = $this->calculateDurationMs($start);

            $this->logger->error('Vario API HTTP request failed', [
                'uri' => (string) $preparedRequest->getUri(),
                'duration_ms' => $durationMs,
                'exception' => $e,
            ]);

            throw new ApiException(
                'HTTP request to Vario API failed',
                previous: $e
            );
        }

        $durationMs = $this->calculateDurationMs($start);

        $this->logResponse($preparedRequest, $response, $durationMs);

        return $this->handleResponse($preparedRequest, $response, $allowRetry);
    }

    private function prepareRequest(RequestInterface $request): RequestInterface
    {
        $preparedRequest = $request->withHeader(
            'X-Requested-With',
            'XMLHttpRequest'
        );

        if ($preparedRequest->hasHeader('Authorization')) {
            $preparedRequest = $preparedRequest->withoutHeader('Authorization');
        }

        $token = $this->tokenStorage->get();

        if ($token === null) {
            return $preparedRequest;
        }

        return $preparedRequest->withHeader(
            'Authorization',
            'Bearer ' . $token->value
        );
    }

    private function rewindRequestBody(RequestInterface $request): void
    {
        $body = $request->getBody();

        if ($body->isSeekable()) {
            $body->rewind();
        }
    }

    private function logRequest(RequestInterface $request): void
    {
        $uri = $request->getUri();

        $this->logger->debug('Vario API request', [
            'method' => $request->getMethod(),
            'uri' => $uri->getPath(),
            'query' => $uri->getQuery(),
            'authorized' => $request->hasHeader('Authorization'),
            'content_length' => $request->getBody()->getSize() ?? 0,
        ]);
    }

    private function logResponse(
        RequestInterface $request,
        ResponseInterface $response,
        float $durationMs
    ): void {
        $uri = $request->getUri();

        $this->logger->debug('Vario API response', [
            'status' => $response->getStatusCode(),
            'uri' => $uri->getPath(),
            'query' => $uri->getQuery(),
            'content_length' => $response->getBody()->getSize() ?? 0,
            'duration_ms' => $durationMs,
        ]);
    }

    private function handleResponse(
        RequestInterface $request,
        ResponseInterface $response,
        bool $allowRetry
    ): ResponseInterface {
        $status = $response->getStatusCode();

        if ($status === 401) {
            return $this->handleUnauthorizedResponse($request, $allowRetry);
        }

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

    private function handleUnauthorizedResponse(
        RequestInterface $request,
        bool $allowRetry
    ): ResponseInterface {
        $this->logger->warning('Vario API unauthorized (401)', [
            'uri' => (string) $request->getUri(),
            'retry' => $allowRetry,
        ]);

        if (!$allowRetry) {
            throw new AuthenticationException(
                'Authentication failed after retry. Check login credentials or company number.'
            );
        }

        $this->reauthenticate();

        return $this->sendWithRetry($request, false);
    }

    private function reauthenticate(): void
    {
        $this->logger->info('Vario API re-authentication triggered');
        $this->logger->debug('Vario auth token cleared before re-authentication');

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

    private function calculateDurationMs(float $start): float
    {
        return round((microtime(true) - $start) * 1000, 2);
    }
}
