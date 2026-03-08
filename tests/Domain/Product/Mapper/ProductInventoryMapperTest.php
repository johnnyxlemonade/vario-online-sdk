<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapper\ProductInventoryMapper;
use Lemonade\Vario\Domain\Product\Mapping\ProductInventoryMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductInventory;
use PHPUnit\Framework\TestCase;

final class ProductInventoryMapperTest extends TestCase
{
    public function testMapsInventory(): void
    {
        $mapping = new ProductInventoryMapping(
            stock: 'stock',
            deliveryTime: 'delivery',
            unit: 'unit',
            warrantyMonths: 'warranty'
        );

        $row = new DatasetRow([
            'stock' => 10,
            'delivery' => 3,
            'unit' => 'pcs',
            'warranty' => 24,
        ]);

        $mapper = new ProductInventoryMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductInventory::class, $result);
        self::assertSame(10, $result->getStock());
        self::assertSame(3, $result->getDeliveryTime());
        self::assertSame('pcs', $result->getUnit());
        self::assertSame(24, $result->getWarrantyMonths());
    }

    public function testReturnsNullWhenAllFieldsMissing(): void
    {
        $mapping = new ProductInventoryMapping(
            stock: 'stock',
            deliveryTime: 'delivery',
            unit: 'unit',
            warrantyMonths: 'warranty'
        );

        $row = new DatasetRow([]);

        $mapper = new ProductInventoryMapper($mapping);

        self::assertNull($mapper->map($row));
    }

    public function testMapsPartialInventory(): void
    {
        $mapping = new ProductInventoryMapping(
            stock: 'stock',
            deliveryTime: 'delivery',
            unit: 'unit',
            warrantyMonths: 'warranty'
        );

        $row = new DatasetRow([
            'stock' => 5,
        ]);

        $mapper = new ProductInventoryMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductInventory::class, $result);
        self::assertSame(5, $result->getStock());
        self::assertNull($result->getDeliveryTime());
        self::assertNull($result->getUnit());
        self::assertNull($result->getWarrantyMonths());
    }
}
