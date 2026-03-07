<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Api;

use Lemonade\Vario\Api\KnownPartyApi;
use Lemonade\Vario\Client\VarioClientInterface;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Enum\VarioEndpoint;
use Lemonade\Vario\Mapper\KnownParty\KnownPartyMapper;
use Lemonade\Vario\Normalizer\KnownParty\KnownPartyInputNormalizer;
use Lemonade\Vario\ValueObject\KnownPartyQuery;
use PHPUnit\Framework\TestCase;

final class KnownPartyApiTest extends TestCase
{
    public function test_query_maps_results(): void
    {
        $client = $this->createMock(VarioClientInterface::class);

        $mapper = new KnownPartyMapper();
        $normalizer = new KnownPartyInputNormalizer();

        $query = new KnownPartyQuery();

        $client
            ->expects(self::once())
            ->method('sendJson')
            ->with(
                HttpMethod::QUERY,
                VarioEndpoint::KnownParty->value,
                $query->toArray()
            )
            ->willReturn([
                ['UUID' => '12345678-1234-1234-1234-1234567890ab'],
            ]);

        $api = new KnownPartyApi($client, $mapper, $normalizer);

        $result = $api->query($query);

        self::assertCount(1, $result);
    }

    public function test_preview_upsert(): void
    {
        $client = $this->createMock(VarioClientInterface::class);

        $mapper = new KnownPartyMapper();
        $normalizer = new KnownPartyInputNormalizer();

        $input = new KnownPartyInput('Test Company');

        $api = new KnownPartyApi($client, $mapper, $normalizer);

        $result = $api->previewUpsert([$input]);

        self::assertCount(1, $result);
        self::assertArrayHasKey('Name', $result[0]);
    }

    public function test_upsert_calls_client_and_maps_result(): void
    {
        $client = $this->createMock(VarioClientInterface::class);

        $mapper = new KnownPartyMapper();
        $normalizer = new KnownPartyInputNormalizer();

        $input = new KnownPartyInput('Test Company');

        $api = new KnownPartyApi($client, $mapper, $normalizer);

        $payload = $api->previewUpsert([$input]);

        $client
            ->expects(self::once())
            ->method('sendJson')
            ->with(
                HttpMethod::PUT,
                VarioEndpoint::KnownParty->value,
                $payload
            )
            ->willReturn([
                [
                    'UUID' => '12345678-1234-1234-1234-1234567890ab',
                    'RecipientObjectID' => 1,
                ],
            ]);

        $result = $api->upsert([$input]);

        self::assertCount(1, $result);
        self::assertSame(
            '12345678-1234-1234-1234-1234567890ab',
            $result[0]->getUuid()
        );
    }
}
