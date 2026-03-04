<?php declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product;

use Lemonade\Vario\Domain\Product\DatasetRow;
use PHPUnit\Framework\TestCase;

final class DatasetRowTest extends TestCase
{
    public function testGetReturnsRawValue(): void
    {
        $row = new DatasetRow([
            'name' => 'Product',
        ]);

        self::assertSame('Product', $row->get('name'));
        self::assertNull($row->get('missing'));
    }

    public function testGetString(): void
    {
        $row = new DatasetRow([
            'name' => 'Product',
        ]);

        self::assertSame('Product', $row->getString('name'));
        self::assertNull($row->getString('missing'));
    }

    public function testGetInt(): void
    {
        $row = new DatasetRow([
            'stock' => '10',
        ]);

        self::assertSame(10, $row->getInt('stock'));
        self::assertNull($row->getInt('missing'));
    }

    public function testGetFloat(): void
    {
        $row = new DatasetRow([
            'price' => '199.9',
        ]);

        self::assertSame(199.9, $row->getFloat('price'));
        self::assertNull($row->getFloat('missing'));
    }

    public function testGetBool(): void
    {
        $row = new DatasetRow([
            'sale' => 1,
        ]);

        self::assertTrue($row->getBool('sale'));
    }
}
