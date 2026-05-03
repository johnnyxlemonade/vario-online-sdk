<?php

declare(strict_types=1);

namespace Lemonade\Vario;

use Lemonade\Vario\Health\VarioHealthResult;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Throwable;

/**
 * Class VarioHealth
 *
 * Lightweight health-check service for the Vario Online API server.
 *
 * This class is intended to be used before creating the authenticated
 * VarioApi facade. It verifies whether the configured base URL is reachable
 * and whether the server responds at the HTTP level.
 *
 * The health check does not perform authentication and does not validate
 * any specific API endpoint, dataset view, permissions or business data.
 * It is designed only to distinguish server/network availability problems
 * from authentication, API and synchronization errors.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario
 * @category    Health
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.12.0
 */
final class VarioHealth
{
    public function __construct(
        private readonly VarioClientConfig $config,
        private readonly ClientInterface $httpClient,
        private readonly RequestFactoryInterface $requestFactory,
    ) {}

    public function checkServer(string $uri = '/'): VarioHealthResult
    {
        $startedAt = microtime(true);

        try {
            $request = $this->requestFactory
                ->createRequest('GET', $this->buildUrl($uri))
                ->withHeader('X-Requested-With', 'XMLHttpRequest');

            $response = $this->httpClient->sendRequest($request);
            $statusCode = $response->getStatusCode();

            if ($statusCode >= 500) {
                return VarioHealthResult::unavailable(
                    reason: 'server_error',
                    message: 'Server responded with HTTP ' . $statusCode,
                    durationMs: $this->durationMs($startedAt),
                    statusCode: $statusCode
                );
            }

            return VarioHealthResult::available(
                durationMs: $this->durationMs($startedAt),
                statusCode: $statusCode
            );
        } catch (ClientExceptionInterface $e) {
            return VarioHealthResult::unavailable(
                reason: $e::class,
                message: $e->getMessage(),
                durationMs: $this->durationMs($startedAt),
                previous: $e
            );
        } catch (Throwable $e) {
            return VarioHealthResult::unavailable(
                reason: $e::class,
                message: $e->getMessage(),
                durationMs: $this->durationMs($startedAt),
                previous: $e
            );
        }
    }

    private function buildUrl(string $uri): string
    {
        return rtrim($this->config->getBaseUrl(), '/') . '/' . ltrim($uri, '/');
    }

    private function durationMs(float $startedAt): float
    {
        return round((microtime(true) - $startedAt) * 1000, 2);
    }
}
