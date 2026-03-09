<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapper\ProductAttributesMapper;
use Lemonade\Vario\Domain\Product\Mapping\ProductAttributesMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductAttributes;
use PHPUnit\Framework\TestCase;

final class ProductAttributesMapperTest extends TestCase
{
    public function testMapsAttributes(): void
    {
        $mapping = new ProductAttributesMapping([
            'color' => 'color',
            'size' => 'size',
        ]);

        $row = new DatasetRow([
            'color' => 'red',
            'size' => 'L',
        ]);

        $mapper = new ProductAttributesMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductAttributes::class, $result);
        self::assertSame('red', $result->get('color'));
        self::assertSame('L', $result->get('size'));
    }

    public function testIgnoresNonScalarValues(): void
    {
        $mapping = new ProductAttributesMapping([
            'color' => 'color',
            'invalid' => 'invalid',
        ]);

        $row = new DatasetRow([
            'color' => 'red',
            'invalid' => ['array'],
        ]);

        $mapper = new ProductAttributesMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductAttributes::class, $result);
        self::assertSame('red', $result->get('color'));
        self::assertNull($result->get('invalid'));
    }

    public function testReturnsAttributesWithNullValueWhenColumnMissing(): void
    {
        $mapping = new ProductAttributesMapping([
            'color' => 'color',
        ]);

        $row = new DatasetRow([]);

        $mapper = new ProductAttributesMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductAttributes::class, $result);
        self::assertTrue($result->has('color'));
        self::assertNull($result->get('color'));
    }

    public function testReturnsNullWhenMappingHasNoAttributes(): void
    {
        $mapping = new ProductAttributesMapping([]);

        $row = new DatasetRow([
            'color' => 'red',
        ]);

        $mapper = new ProductAttributesMapper($mapping);

        self::assertNull($mapper->map($row));
    }
}
