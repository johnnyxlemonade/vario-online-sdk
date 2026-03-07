<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Api;

use Lemonade\Vario\Api\OutgoingInvoiceApi;
use Lemonade\Vario\Client\VarioClientInterface;
use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Enum\VarioEndpoint;
use Lemonade\Vario\ValueObject\OutgoingInvoiceQuery;
use PHPUnit\Framework\TestCase;

final class OutgoingInvoiceApiTest extends TestCase
{
    public function test_query_calls_client(): void
    {
        $client = $this->createMock(VarioClientInterface::class);

        $query = new OutgoingInvoiceQuery();

        $client
            ->expects(self::once())
            ->method('sendJson')
            ->with(
                HttpMethod::QUERY,
                VarioEndpoint::OutgoingInvoice->value,
                $query->toArray()
            )
            ->willReturn([
                ['id' => 1],
                ['id' => 2],
            ]);

        $api = new OutgoingInvoiceApi($client);

        $result = $api->query($query);

        self::assertCount(2, $result);
        self::assertSame(1, $result[0]['id']);
    }

    public function test_upsert_calls_client(): void
    {
        $client = $this->createMock(VarioClientInterface::class);

        $payload = [
            ['id' => 10],
            ['id' => 11],
        ];

        $client
            ->expects(self::once())
            ->method('sendJson')
            ->with(
                HttpMethod::PUT,
                VarioEndpoint::OutgoingInvoice->value,
                $payload
            )
            ->willReturn($payload);

        $api = new OutgoingInvoiceApi($client);

        $result = $api->upsert($payload);

        self::assertSame($payload, $result);
    }
}
