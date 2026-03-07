<?php

declare(strict_types=1);

namespace Lemonade\Vario\Api;

use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Enum\VarioEndpoint;
use Lemonade\Vario\ValueObject\OutgoingInvoiceQuery;

/**
 * Class OutgoingInvoiceApi
 *
 * API modul pro práci s vydanými doklady (OutgoingInvoice)
 * ve Vario Online.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Api
 * @category    API
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class OutgoingInvoiceApi extends AbstractApi
{
    /**
     * @return list<array<string,mixed>>
     */
    public function query(OutgoingInvoiceQuery $query): array
    {
        $result = $this->sendJson(
            HttpMethod::QUERY,
            VarioEndpoint::OutgoingInvoice,
            $query->toArray()
        );

        /** @var list<array<string,mixed>> $result */
        return $result;
    }

    /**
     * @param list<array<string,mixed>> $payload
     * @return list<array<string,mixed>>
     */
    public function upsert(array $payload): array
    {
        $result = $this->sendJson(
            HttpMethod::PUT,
            VarioEndpoint::OutgoingInvoice,
            $payload
        );

        /** @var list<array<string,mixed>> $result */
        return $result;
    }
}
