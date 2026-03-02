<?php declare(strict_types=1);

namespace Lemonade\Vario\Api;

use Lemonade\Vario\Client\VarioClientInterface;
use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Enum\VarioEndpoint;

/**
 * Class AbstractApi
 *
 * Základní abstraktní třída pro všechny API moduly Vario.
 * Poskytuje společné závislosti a sdílenou logiku
 * pro odesílání JSON requestů.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Api
 * @category    Abstract
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
