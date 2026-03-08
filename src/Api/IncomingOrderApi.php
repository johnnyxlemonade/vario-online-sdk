<?php

declare(strict_types=1);

namespace Lemonade\Vario\Api;

use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Enum\VarioEndpoint;
use Lemonade\Vario\ValueObject\IncomingOrderQuery;

/**
 * Class IncomingOrderApi
 *
 * API module providing access to IncomingOrder endpoints
 * of the Vario Online API.
 *
 * Incoming orders represent purchase or sales orders processed
 * within the Vario ERP system. This module exposes operations
 * for querying existing orders and creating or updating them.
 *
 * Responsibilities of this class include:
 *
 *  - executing order queries using IncomingOrderQuery objects
 *  - sending bulk upsert requests for incoming orders
 *  - delegating transport communication to the VarioClient
 *
 * The API operates on raw transport payloads (`array<string,mixed>`).
 * Mapping to domain models should be handled by higher-level
 * application layers when needed.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Api
 * @category    API
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderApi extends AbstractApi
{
    /**
     * @return list<array<string,mixed>>
     */
    public function query(IncomingOrderQuery $query): array
    {
        $result = $this->sendJson(
            HttpMethod::QUERY,
            VarioEndpoint::IncomingOrder,
            $query->toArray()
        );

        /** @var list<array<string,mixed>> $result */
        return $result;
    }

    /**
     * @param list<array<string,mixed>> $orders
     * @return list<array<string,mixed>>
     */
    public function upsert(array $orders): array
    {
        $result = $this->sendJson(
            HttpMethod::PUT,
            VarioEndpoint::IncomingOrder,
            $orders
        );

        /** @var list<array<string,mixed>> $result */
        return $result;
    }
}
