<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Api;

use Lemonade\Vario\Api\DatasetViewApi;
use Lemonade\Vario\Client\VarioClientInterface;
use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Enum\VarioEndpoint;
use Lemonade\Vario\ValueObject\CustomDatasetView;
use Lemonade\Vario\ValueObject\DatasetViewQuery;
use PHPUnit\Framework\TestCase;

final class DatasetViewApiTest extends TestCase
{
    public function test_get_calls_client(): void
    {
        $client = $this->createMock(VarioClientInterface::class);

        $query = DatasetViewQuery::for(
            new CustomDatasetView('Test/View')
        );

        $client
            ->expects(self::once())
            ->method('sendQuery')
            ->with(
                HttpMethod::GET,
                VarioEndpoint::DatasetView->value,
                $query->toArray()
            )
            ->willReturn([
                'Data' => [['id' => 1]],
                'Pager' => ['PageCount' => 1],
            ]);

        $api = new DatasetViewApi($client);

        $result = $api->get($query);

        self::assertSame([['id' => 1]], $result['Data'] ?? []);
    }

    public function test_iterate_paginates_results(): void
    {
        $client = $this->createMock(VarioClientInterface::class);

        $query = DatasetViewQuery::for(
            new CustomDatasetView('Test/View')
        );

        $client
            ->expects(self::exactly(2))
            ->method('sendQuery')
            ->willReturnOnConsecutiveCalls(
                [
                    'Data' => [
                        ['id' => 1],
                        ['id' => 2],
                    ],
                    'Pager' => ['PageCount' => 2],
                ],
                [
                    'Data' => [
                        ['id' => 3],
                    ],
                    'Pager' => ['PageCount' => 2],
                ]
            );

        $api = new DatasetViewApi($client);

        $rows = iterator_to_array(
            $api->iterate($query, 2)
        );

        self::assertCount(3, $rows);
        self::assertSame(1, $rows[0]['id']);
        self::assertSame(2, $rows[1]['id']);
        self::assertSame(3, $rows[2]['id']);
    }
}
