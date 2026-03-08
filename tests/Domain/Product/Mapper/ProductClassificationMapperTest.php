<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapper\ProductClassificationMapper;
use Lemonade\Vario\Domain\Product\Mapping\ProductClassificationMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductClassification;
use PHPUnit\Framework\TestCase;

final class ProductClassificationMapperTest extends TestCase
{
    public function testMapsClassification(): void
    {
        $mapping = new ProductClassificationMapping(
            categoryId: 'cat_id',
            categoryName: 'cat_name',
            brand: 'brand'
        );

        $row = new DatasetRow([
            'cat_id' => '10',
            'cat_name' => 'Electronics',
            'brand' => 'Acme',
        ]);

        $mapper = new ProductClassificationMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductClassification::class, $result);
        self::assertSame('10', $result->getCategoryId());
        self::assertSame('Electronics', $result->getCategoryName());
        self::assertSame('Acme', $result->getBrand());
    }

    public function testReturnsNullWhenNoFieldsPresent(): void
    {
        $mapping = new ProductClassificationMapping(
            categoryId: 'cat_id',
            categoryName: 'cat_name',
            brand: 'brand'
        );

        $row = new DatasetRow([]);

        $mapper = new ProductClassificationMapper($mapping);

        self::assertNull($mapper->map($row));
    }

    public function testMapsPartialData(): void
    {
        $mapping = new ProductClassificationMapping(
            categoryId: 'cat_id',
            categoryName: 'cat_name',
            brand: 'brand'
        );

        $row = new DatasetRow([
            'brand' => 'Acme',
        ]);

        $mapper = new ProductClassificationMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductClassification::class, $result);
        self::assertNull($result->getCategoryId());
        self::assertNull($result->getCategoryName());
        self::assertSame('Acme', $result->getBrand());
    }
}
