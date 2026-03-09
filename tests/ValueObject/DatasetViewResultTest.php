<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\ValueObject;

use Lemonade\Vario\ValueObject\DatasetViewResult;
use PHPUnit\Framework\TestCase;

final class DatasetViewResultTest extends TestCase
{
    public function testGetters(): void
    {
        $rows = [
            [
                'ID_Produktu' => 'abc',
                'Nazev_produktu' => 'Test product',
            ],
        ];

        $pager = [
            'PageCount' => 10,
            'PageIndex' => 2,
            'PageLength' => 100,
            'RecordCount' => 999,
        ];

        $result = new DatasetViewResult($rows, $pager);

        self::assertSame($rows, $result->getRows());
        self::assertSame($pager, $result->getPager());
        self::assertTrue($result->hasRows());

        self::assertSame(10, $result->getPageCount());
        self::assertSame(2, $result->getPageIndex());
        self::assertSame(100, $result->getPageLength());
        self::assertSame(999, $result->getRecordCount());
    }

    public function testReturnsDefaultsForMissingPagerValues(): void
    {
        $result = new DatasetViewResult([]);

        self::assertSame([], $result->getRows());
        self::assertSame([], $result->getPager());
        self::assertFalse($result->hasRows());

        self::assertSame(0, $result->getPageCount());
        self::assertSame(0, $result->getPageIndex());
        self::assertSame(0, $result->getPageLength());
        self::assertSame(0, $result->getRecordCount());
    }

    public function testCastsPagerValuesToInt(): void
    {
        $result = new DatasetViewResult([], [
            'PageCount' => '5',
            'PageIndex' => 1.0,
            'PageLength' => '50',
            'RecordCount' => 250.0,
        ]);

        self::assertSame(5, $result->getPageCount());
        self::assertSame(1, $result->getPageIndex());
        self::assertSame(50, $result->getPageLength());
        self::assertSame(250, $result->getRecordCount());
    }

    public function testToArray(): void
    {
        $rows = [
            [
                'ID_Produktu' => 'abc',
                'Nazev_produktu' => 'Test product',
            ],
        ];

        $pager = [
            'PageCount' => 1,
            'PageIndex' => 0,
            'PageLength' => 100,
            'RecordCount' => 1,
        ];

        $result = new DatasetViewResult($rows, $pager);

        self::assertSame([
            'Data' => $rows,
            'Pager' => $pager,
        ], $result->toArray());
    }
}
