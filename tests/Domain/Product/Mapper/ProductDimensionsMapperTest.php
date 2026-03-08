<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapper\ProductDimensionsMapper;
use Lemonade\Vario\Domain\Product\Mapping\ProductDimensionsMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductDimensions;
use PHPUnit\Framework\TestCase;

final class ProductDimensionsMapperTest extends TestCase
{
    public function testMapsDimensions(): void
    {
        $mapping = new ProductDimensionsMapping(
            width: 'width',
            height: 'height',
            depth: 'depth',
            weightKg: 'weight'
        );

        $row = new DatasetRow([
            'width' => 10,
            'height' => 20,
            'depth' => 30,
            'weight' => 1.5,
        ]);

        $mapper = new ProductDimensionsMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductDimensions::class, $result);
        self::assertSame(10.0, $result->getWidth());
        self::assertSame(20.0, $result->getHeight());
        self::assertSame(30.0, $result->getDepth());
        self::assertSame(1500.0, $result->getWeightGrams());
    }

    public function testReturnsNullWhenAllFieldsMissing(): void
    {
        $mapping = new ProductDimensionsMapping(
            width: 'width',
            height: 'height',
            depth: 'depth',
            weightKg: 'weight'
        );

        $row = new DatasetRow([]);

        $mapper = new ProductDimensionsMapper($mapping);

        self::assertNull($mapper->map($row));
    }

    public function testMapsPartialDimensions(): void
    {
        $mapping = new ProductDimensionsMapping(
            width: 'width',
            height: 'height',
            depth: 'depth',
            weightKg: 'weight'
        );

        $row = new DatasetRow([
            'width' => 10,
        ]);

        $mapper = new ProductDimensionsMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductDimensions::class, $result);
        self::assertSame(10.0, $result->getWidth());
        self::assertNull($result->getHeight());
        self::assertNull($result->getDepth());
        self::assertNull($result->getWeightGrams());
    }
}
