<?php declare(strict_types=1);

namespace Lemonade\Vario\Client;

use Lemonade\Vario\Auth\TokenStorageInterface;
use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Exception\ApiException;
use Lemonade\Vario\Exception\AuthenticationException;
use Lemonade\Vario\Exception\ForbiddenException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class VarioClient implements VarioClientInterface
{
    private readonly \Closure $reauthCallback;

    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly RequestFactoryInterface $requestFactory,
        callable $reauthCallback
    ) {
        // normalize callable → Closure (PHP 8.1 safe storage)
        $this->reauthCallback = \Closure::fromCallable($reauthCallback);
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
     * Decode JSON response and guarantee array result.
     *
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

    /**
     * Send request with optional retry after re-authentication.
     */
    private function sendWithRetry(
        RequestInterface $request,
        bool $allowRetry
    ): ResponseInterface {
        $request = $this->prepareRequest($request);

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new ApiException(
                'HTTP request to Vario API failed',
                previous: $e
            );
        }

        $status = $response->getStatusCode();

        // 401 handling
        if ($status === 401) {

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
        try {
            ($this->reauthCallback)();
        } catch (\Throwable $e) {
            throw new AuthenticationException(
                'Re-authentication failed',
                previous: $e
            );
        }
    }
}
