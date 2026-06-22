<?php

declare(strict_types=1);

namespace Lemonade\Vario\Api;

use Lemonade\Vario\Client\VarioClientInterface;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrder;
use Lemonade\Vario\Domain\IncomingOrder\Result\IncomingOrderUpsertResult;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderInput;
use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Enum\VarioEndpoint;
use Lemonade\Vario\Mapper\IncomingOrder\IncomingOrderMapper;
use Lemonade\Vario\Normalizer\IncomingOrder\IncomingOrderInputNormalizer;
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
 * - executing order queries using IncomingOrderQuery objects
 * - normalizing IncomingOrderInput objects into transport payloads
 * - sending bulk upsert requests for incoming orders
 * - mapping API responses into IncomingOrder domain objects
 * - mapping upsert results into IncomingOrderUpsertResult objects
 * - delegating transport communication to the VarioClient
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Api
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderApi extends AbstractApi
{
    private readonly IncomingOrderMapper $mapper;
    private readonly IncomingOrderInputNormalizer $normalizer;

    public function __construct(
        VarioClientInterface $client,
        IncomingOrderMapper $mapper,
        IncomingOrderInputNormalizer $normalizer,
    ) {
        parent::__construct($client);

        $this->mapper = $mapper;
        $this->normalizer = $normalizer;
    }

    /**
     * @return list<IncomingOrder>
     */
    public function query(IncomingOrderQuery $query): array
    {
        $result = $this->sendJson(
            HttpMethod::QUERY,
            VarioEndpoint::IncomingOrder,
            $query->toArray(),
        );

        /** @var list<array<string,mixed>> $result */
        return $this->mapList($result);
    }

    /**
     * @param list<IncomingOrderInput> $inputs
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
     * @param list<IncomingOrderInput> $inputs
     * @return list<IncomingOrderUpsertResult>
     */
    public function upsert(array $inputs): array
    {
        $payload = $this->previewUpsert($inputs);

        $result = $this->sendJson(
            HttpMethod::PUT,
            VarioEndpoint::IncomingOrder,
            $payload,
        );

        /** @var list<array<string,mixed>> $result */
        return $this->mapUpsertResult($result);
    }

    /**
     * @param list<array<string,mixed>> $rows
     * @return list<IncomingOrder>
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
     * @return list<IncomingOrderUpsertResult>
     */
    private function mapUpsertResult(array $rows): array
    {
        $mapped = [];

        foreach ($rows as $row) {
            $mapped[] = IncomingOrderUpsertResult::fromArray($row);
        }

        return $mapped;
    }
}
