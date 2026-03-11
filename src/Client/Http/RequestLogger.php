<?php

declare(strict_types=1);

namespace Lemonade\Vario\Client\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

final class RequestLogger
{
    public function logRequest(LoggerInterface $logger, RequestInterface $request): void
    {
        $uri = $request->getUri();

        $logger->debug('Vario API request', [
            'method' => $request->getMethod(),
            'uri' => $uri->getPath(),
            'query' => $uri->getQuery(),
            'authorized' => $request->hasHeader('Authorization'),
            'content_length' => $request->getBody()->getSize() ?? 0,
        ]);
    }

    public function logResponse(
        LoggerInterface $logger,
        RequestInterface $request,
        ResponseInterface $response,
        float $durationMs
    ): void {

        $uri = $request->getUri();

        $logger->debug('Vario API response', [
            'status' => $response->getStatusCode(),
            'uri' => $uri->getPath(),
            'query' => $uri->getQuery(),
            'content_length' => $response->getBody()->getSize() ?? 0,
            'duration_ms' => $durationMs,
        ]);
    }
}
