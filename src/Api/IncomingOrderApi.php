<?php declare(strict_types=1);

namespace Lemonade\Vario\Api;

use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Enum\VarioEndpoint;
use Lemonade\Vario\ValueObject\IncomingOrderQuery;

/**
 * Class IncomingOrderApi
 *
 * API modul pro práci s příchozími objednávkami (IncomingOrder)
 * ve Vario Online.
 *
 * Umožňuje:
 *  - dotazování na existující objednávky,
 *  - vytváření a aktualizaci objednávek (upsert).
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Api
 * @category    Api
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
            HttpMethod::POST,
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
