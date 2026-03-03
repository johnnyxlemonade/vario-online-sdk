<?php declare(strict_types=1);

namespace Lemonade\Vario\Api;

use Lemonade\Vario\Client\VarioClientInterface;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInputNormalizer;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInterface;
use Lemonade\Vario\Domain\KnownParty\KnownPartyMapper;
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
    private readonly KnownPartyMapper $mapper;
    private readonly KnownPartyInputNormalizer $normalizer;

    public function __construct(
        VarioClientInterface $client,
        KnownPartyMapper $mapper,
        KnownPartyInputNormalizer $normalizer,
    ) {
        parent::__construct($client);

        $this->mapper = $mapper;
        $this->normalizer = $normalizer;
    }

    /* ============================================
     * READ
     * ============================================ */

    /** @return list<KnownPartyInterface> */
    public function query(KnownPartyQuery $query): array
    {
        $result = $this->sendJson(
            HttpMethod::QUERY,
            VarioEndpoint::KnownParty,
            $query->toArray()
        );

        /** @var list<array<string,mixed>> $result */
        return $this->mapList($result);
    }

    /* ============================================
     * WRITE (SAFE PREVIEW)
     * ============================================ */

    /**
     * Builds payload without sending request.
     *
     * @param list<KnownPartyInput> $inputs
     * @return list<array<string,mixed>>
     */
    public function previewUpsert(array $inputs): array
    {
        $payload = [];

        foreach ($inputs as $input) {
            $payload[] = $this->normalizer->normalize($input);
        }

        return $payload;
    }

    /**
     * @param list<KnownPartyInput> $inputs
     * @return list<KnownPartyInterface>
     */
    public function upsert(array $inputs): array
    {
        $payload = $this->previewUpsert($inputs);

        $result = $this->sendJson(
            HttpMethod::PUT,
            VarioEndpoint::KnownParty,
            $payload
        );

        /** @var list<array<string,mixed>> $result */
        return $this->mapList($result);
    }

    /**
     * @param list<array<string,mixed>> $rows
     * @return list<KnownPartyInterface>
     */
    private function mapList(array $rows): array
    {
        $mapped = [];

        foreach ($rows as $row) {
            $mapped[] = $this->mapper->map($row);
        }

        return $mapped;
    }
}
