<?php

declare(strict_types=1);

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

    public function testGetNullableString(): void
    {
        $row = new DatasetRow([
            'name' => 'Product',
            'empty' => '',
        ]);

        self::assertSame('Product', $row->getNullableString('name'));
        self::assertNull($row->getNullableString('empty'));
        self::assertNull($row->getNullableString('missing'));
    }

    public function testGetNullableFloat(): void
    {
        $row = new DatasetRow([
            'price' => '10.5',
            'invalid' => 'abc',
        ]);

        self::assertSame(10.5, $row->getNullableFloat('price'));
        self::assertNull($row->getNullableFloat('invalid'));
        self::assertNull($row->getNullableFloat('missing'));
    }

    public function testHas(): void
    {
        $row = new DatasetRow([
            'name' => 'Product',
            'nullField' => null,
        ]);

        self::assertTrue($row->has('name'));
        self::assertTrue($row->has('nullField'));
        self::assertFalse($row->has('missing'));
    }

    public function testToArray(): void
    {
        $data = [
            'name' => 'Product',
            'price' => 100,
        ];

        $row = new DatasetRow($data);

        self::assertSame($data, $row->toArray());
    }

    public function testNonScalarReturnsNull(): void
    {
        $row = new DatasetRow([
            'array' => ['x'],
            'object' => new \stdClass(),
        ]);

        self::assertNull($row->getString('array'));
        self::assertNull($row->getInt('object'));
        self::assertNull($row->getFloat('array'));
    }

    public function testEmptyStringNormalizesToNull(): void
    {
        $row = new DatasetRow([
            'empty' => '',
        ]);

        self::assertNull($row->getString('empty'));
        self::assertNull($row->getInt('empty'));
        self::assertNull($row->getFloat('empty'));
        self::assertNull($row->getBool('empty'));
    }

    public function testGetIntReturnsNullForNonNumericScalar(): void
    {
        $row = new DatasetRow([
            'value' => 'abc',
        ]);

        self::assertNull($row->getInt('value'));
    }

    public function testGetIntReturnsNullForNonNumeric(): void
    {
        $row = new DatasetRow([
            'invalid' => 'abc',
        ]);

        self::assertNull($row->getInt('invalid'));
    }
}
