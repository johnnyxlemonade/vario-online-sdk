<?php

declare(strict_types=1);

namespace Lemonade\Vario\Client;

use Lemonade\Vario\Enum\HttpMethod;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface VarioClientInterface
 *
 * Transport abstraction used by the SDK to communicate with
 * the Vario Online API.
 *
 * This interface defines the minimal contract required by
 * API modules to send HTTP requests and receive decoded
 * responses without depending on a specific HTTP client
 * implementation.
 *
 * The concrete implementation is provided by VarioClient,
 * which handles authentication, retry logic and request
 * preparation on top of a PSR-compatible HTTP adapter.
 *
 * API modules interact only with this interface, ensuring
 * that the transport layer can be replaced or mocked
 * without affecting the higher-level domain logic.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Client
 * @category    Transport
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrák
 * @license     MIT
 * @since       1.0
 */
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
     * @return array<string,mixed>|list<mixed>
     */
    public function sendQuery(
        HttpMethod $method,
        string $uri,
        array $query
    ): array;
}
