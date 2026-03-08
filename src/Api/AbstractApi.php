<?php

declare(strict_types=1);

namespace Lemonade\Vario\Api;

use Lemonade\Vario\Client\VarioClientInterface;
use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Enum\VarioEndpoint;

/**
 * Class AbstractApi
 *
 * Base class for all Vario API modules.
 *
 * This class provides shared infrastructure used by higher-level
 * API services such as DatasetViewApi, KnownPartyApi and others.
 *
 * Its main responsibility is delegating HTTP communication to the
 * underlying VarioClient while keeping API modules focused purely
 * on domain operations.
 *
 * Responsibilities of this class include:
 *
 *  - providing access to the underlying VarioClient transport
 *  - sending JSON API requests
 *  - sending query-based requests
 *  - standardizing endpoint usage via VarioEndpoint enum
 *
 * API modules extending this class should only implement domain-level
 * operations and response mapping logic.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Api
 * @category    API
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
abstract class AbstractApi
{
    public function __construct(
        protected readonly VarioClientInterface $client
    ) {}

    /**
     * @param array<string,mixed>|list<mixed>|null $payload
     * @return array<string,mixed>|list<mixed>
     */
    protected function sendJson(
        HttpMethod $method,
        VarioEndpoint $endpoint,
        ?array $payload = null
    ): array {
        return $this->client->sendJson(
            $method,
            $endpoint->value,
            $payload
        );
    }

    /**
     * @param array<string,mixed> $query
     * @return array<string,mixed>
     */
    protected function sendQuery(
        HttpMethod $method,
        VarioEndpoint $endpoint,
        array $query
    ): array {
        return $this->client->sendQuery(
            $method,
            $endpoint->value,
            $query
        );
    }
}
