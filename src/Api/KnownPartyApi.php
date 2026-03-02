<?php declare(strict_types=1);

namespace Lemonade\Vario\Api;

use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Enum\VarioEndpoint;
use Lemonade\Vario\ValueObject\KnownPartyQuery;

/**
 * Class KnownPartyApi
 *
 * API modul pro práci se známými obchodními partnery (KnownParty)
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
final class KnownPartyApi extends AbstractApi
{
    /** @return list<array<string,mixed>> */
    public function query(KnownPartyQuery $query): array
    {
        $result = $this->sendJson(
            HttpMethod::POST,
            VarioEndpoint::KnownParty,
            $query->toArray()
        );

        /** @var list<array<string,mixed>> $result */
        return $result;
    }

    /**
     * @param list<array<string,mixed>> $parties
     * @return list<array<string,mixed>>
     */
    public function upsert(array $parties): array
    {
        $result = $this->sendJson(
            HttpMethod::PUT,
            VarioEndpoint::KnownParty,
            $parties
        );

        /** @var list<array<string,mixed>> $result */
        return $result;
    }
}
