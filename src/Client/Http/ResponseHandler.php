<?php

declare(strict_types=1);

namespace Lemonade\Vario\Client\Http;

use Lemonade\Vario\Enum\HttpStatus;
use Lemonade\Vario\Exception\ApiException;
use Lemonade\Vario\Exception\ForbiddenException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

final class ResponseHandler
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {}

    /**
     * @param callable():ResponseInterface $onUnauthorized
     */
    public function handle(
        RequestInterface $request,
        ResponseInterface $response,
        callable $onUnauthorized
    ): ResponseInterface {

        $statusCode = $response->getStatusCode();
        $status = HttpStatus::tryFrom($statusCode);

        return match ($status) {

            HttpStatus::UNAUTHORIZED =>
            $onUnauthorized(),

            HttpStatus::FORBIDDEN =>
            $this->throwForbidden($request, $response),

            default =>
            $this->handleGeneric($request, $response, $statusCode),
        };
    }

    private function throwForbidden(
        RequestInterface $request,
        ResponseInterface $response
    ): never {

        $body = (string) $response->getBody();

        $this->logger->warning('Vario API forbidden', [
            'uri' => (string) $request->getUri(),
        ]);

        throw new ForbiddenException(
            sprintf(
                "Vario API forbidden\nURI: %s",
                (string) $request->getUri()
            ),
            HttpStatus::FORBIDDEN->value,
            $body
        );
    }

    private function handleGeneric(
        RequestInterface $request,
        ResponseInterface $response,
        int $status
    ): ResponseInterface {

        if ($status < 400) {
            return $response;
        }

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
}
