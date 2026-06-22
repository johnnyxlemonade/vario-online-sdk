<?php

declare(strict_types=1);

namespace Lemonade\Vario\Api;

use Lemonade\Vario\Client\VarioClientInterface;
use Lemonade\Vario\Domain\OutgoingQuotation\Result\OutgoingQuotationUpsertResult;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationInput;
use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Enum\VarioEndpoint;
use Lemonade\Vario\Normalizer\OutgoingQuotation\OutgoingQuotationInputNormalizer;
use Lemonade\Vario\ValueObject\OutgoingQuotationQuery;

final class OutgoingQuotationApi extends AbstractApi
{
    public function __construct(
        VarioClientInterface $client,
        private readonly OutgoingQuotationInputNormalizer $normalizer,
    ) {
        parent::__construct($client);
    }

    /**
     * @return list<array<string,mixed>>
     */
    public function query(OutgoingQuotationQuery $query): array
    {
        $result = $this->sendJson(
            HttpMethod::QUERY,
            VarioEndpoint::OutgoingQuotation,
            $query->toArray(),
        );

        /** @var list<array<string,mixed>> $result */
        return $result;
    }

    /**
     * @param list<OutgoingQuotationInput> $inputs
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
     * @param list<OutgoingQuotationInput> $inputs
     * @return list<OutgoingQuotationUpsertResult>
     */
    public function upsert(array $inputs): array
    {
        $payload = $this->previewUpsert($inputs);

        $result = $this->sendJson(
            HttpMethod::PUT,
            VarioEndpoint::OutgoingQuotation,
            $payload,
        );

        /** @var list<array<string,mixed>> $result */
        return $this->mapUpsertResult($result);
    }

    /**
     * @param list<array<string,mixed>> $rows
     * @return list<OutgoingQuotationUpsertResult>
     */
    private function mapUpsertResult(array $rows): array
    {
        $mapped = [];

        foreach ($rows as $row) {
            $mapped[] = OutgoingQuotationUpsertResult::fromArray($row);
        }

        return $mapped;
    }
}
