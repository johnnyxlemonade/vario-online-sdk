<?php

declare(strict_types=1);

namespace Lemonade\Vario\Client;

use Closure;
use Lemonade\Vario\Auth\Storage\TokenStorageInterface;
use Lemonade\Vario\Client\Http\RequestAuthenticator;
use Lemonade\Vario\Client\Http\RequestLogger;
use Lemonade\Vario\Client\Http\ResponseHandler;
use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Exception\ApiException;
use Lemonade\Vario\Exception\AuthenticationException;
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
 * The client orchestrates the full lifecycle of an HTTP request:
 *
 *  - building PSR-7 HTTP requests
 *  - authenticating requests via RequestAuthenticator
 *  - sending requests using a PSR-18 HTTP client
 *  - structured logging of requests and responses
 *  - delegating HTTP response handling to ResponseHandler
 *  - automatic retry when authentication expires (401 responses)
 *
 * Responsibilities are intentionally split across dedicated components:
 *
 *  - RequestAuthenticator → attaches the Authorization header
 *  - RequestLogger        → logs outgoing requests and incoming responses
 *  - ResponseHandler      → interprets HTTP status codes and raises exceptions
 *
 * The implementation is fully based on PSR standards:
 *
 *  - PSR-7  HTTP messages
 *  - PSR-17 HTTP factories
 *  - PSR-18 HTTP client
 *  - PSR-3  logging
 *
 * This class intentionally remains transport-focused and does not contain
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
        private readonly RequestAuthenticator $requestAuthenticator,
        private readonly RequestLogger $requestLogger,
        private readonly ResponseHandler $responseHandler,
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

        $preparedRequest = $this->requestAuthenticator->authenticate(
            $request,
            $this->tokenStorage
        );

        $this->rewindRequestBody($preparedRequest);

        $this->requestLogger->logRequest(
            $this->logger,
            $preparedRequest
        );

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

        $this->requestLogger->logResponse(
            $this->logger,
            $preparedRequest,
            $response,
            $durationMs
        );

        return $this->responseHandler->handle(
            $preparedRequest,
            $response,
            fn(): ResponseInterface =>
            $this->handleUnauthorizedResponse($preparedRequest, $allowRetry)
        );
    }

    private function rewindRequestBody(RequestInterface $request): void
    {
        $body = $request->getBody();

        if ($body->isSeekable()) {
            $body->rewind();
        }
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
