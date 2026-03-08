<?php

declare(strict_types=1);

namespace Lemonade\Vario\Api;

use Lemonade\Vario\Client\VarioClientInterface;
use Lemonade\Vario\Domain\KnownParty\KnownParty;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\KnownParty\KnownPartyUpsertResult;
use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Enum\VarioEndpoint;
use Lemonade\Vario\Mapper\KnownParty\KnownPartyMapper;
use Lemonade\Vario\Normalizer\KnownParty\KnownPartyInputNormalizer;
use Lemonade\Vario\ValueObject\KnownPartyQuery;

/**
 * Class KnownPartyApi
 *
 * API module providing access to KnownParty endpoints of the
 * Vario Online API.
 *
 * KnownParty represents a generic business entity within Vario ERP
 * (customer, supplier, contact person, etc.). This module exposes
 * operations for querying existing parties and performing bulk
 * upsert operations.
 *
 * Responsibilities of this class include:
 *
 *  - executing KnownParty queries
 *  - normalizing KnownPartyInput objects into transport payloads
 *  - sending upsert requests to the API
 *  - mapping API responses into KnownParty domain objects
 *  - mapping upsert results into KnownPartyUpsertResult objects
 *
 * Transport communication is delegated to the underlying VarioClient,
 * while domain mapping is handled by KnownPartyMapper and
 * KnownPartyInputNormalizer.
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

    /** @return list<KnownParty> */
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

    /**
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
     * @return list<KnownPartyUpsertResult>
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
        return $this->mapUpsertResult($result);
    }

    /**
     * @param list<array<string,mixed>> $rows
     * @return list<KnownParty>
     */
    private function mapList(array $rows): array
    {
        $mapped = [];

        foreach ($rows as $row) {
            $mapped[] = $this->mapper->map($row);
        }

        return $mapped;
    }

    /**
     * @param list<array<string,mixed>> $rows
     * @return list<KnownPartyUpsertResult>
     */
    private function mapUpsertResult(array $rows): array
    {
        $mapped = [];

        foreach ($rows as $row) {
            $mapped[] = KnownPartyUpsertResult::fromArray($row);
        }

        return $mapped;
    }
}
