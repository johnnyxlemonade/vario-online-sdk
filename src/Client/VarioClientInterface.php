<?php declare(strict_types=1);

namespace Lemonade\Vario\Client;

use Lemonade\Vario\Enum\HttpMethod;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface VarioClientInterface
{
    /**
     * Send raw PSR request.
     */
    public function send(RequestInterface $request): ResponseInterface;

    /**
     * Send JSON request and decode response.
     *
     * @param array<string,mixed>|list<mixed>|null $payload
     * @return array<string,mixed>|list<mixed>
     */
    public function sendJson(
        HttpMethod $method,
        string $uri,
        ?array $payload = null
    ): array;

    /**
     * Send query request and decode response.
     *
     * @param array<string,mixed> $query
     * @return array<string,mixed>
     */
    public function sendQuery(
        HttpMethod $method,
        string $uri,
        array $query
    ): array;
}
