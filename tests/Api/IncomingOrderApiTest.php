<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Api;

use Lemonade\Vario\Api\IncomingOrderApi;
use Lemonade\Vario\Client\VarioClientInterface;
use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Enum\VarioEndpoint;
use Lemonade\Vario\ValueObject\IncomingOrderQuery;
use PHPUnit\Framework\TestCase;

final class IncomingOrderApiTest extends TestCase
{
    public function test_query_calls_client(): void
    {
        $client = $this->createMock(VarioClientInterface::class);

        $query = new IncomingOrderQuery();

        $client
            ->expects(self::once())
            ->method('sendJson')
            ->with(
                HttpMethod::QUERY,
                VarioEndpoint::IncomingOrder->value,
                $query->toArray()
            )
            ->willReturn([
                ['id' => 1],
                ['id' => 2],
            ]);

        $api = new IncomingOrderApi($client);

        $result = $api->query($query);

        self::assertCount(2, $result);
        self::assertSame(1, $result[0]['id']);
    }

    public function test_upsert_calls_client(): void
    {
        $client = $this->createMock(VarioClientInterface::class);

        $orders = [
            ['id' => 10],
            ['id' => 11],
        ];

        $client
            ->expects(self::once())
            ->method('sendJson')
            ->with(
                HttpMethod::PUT,
                VarioEndpoint::IncomingOrder->value,
                $orders
            )
            ->willReturn($orders);

        $api = new IncomingOrderApi($client);

        $result = $api->upsert($orders);

        self::assertSame($orders, $result);
    }
}
