<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\ValueObject;

use Lemonade\Vario\Domain\Product\ValueObject\ProductInventory;
use PHPUnit\Framework\TestCase;

final class ProductInventoryTest extends TestCase
{
    public function testGetters(): void
    {
        $inventory = new ProductInventory(
            stock: 10,
            deliveryTime: 3,
            unit: 'pcs',
            warrantyMonths: 24
        );

        self::assertSame(10, $inventory->getStock());
        self::assertSame(3, $inventory->getDeliveryTime());
        self::assertSame('pcs', $inventory->getUnit());
        self::assertSame(24, $inventory->getWarrantyMonths());
    }

    public function testNullValues(): void
    {
        $inventory = new ProductInventory(
            stock: null,
            deliveryTime: null,
            unit: null,
            warrantyMonths: null
        );

        self::assertNull($inventory->getStock());
        self::assertNull($inventory->getDeliveryTime());
        self::assertNull($inventory->getUnit());
        self::assertNull($inventory->getWarrantyMonths());
    }

    public function testToArray(): void
    {
        $inventory = new ProductInventory(
            stock: 10,
            deliveryTime: 3,
            unit: 'pcs',
            warrantyMonths: 24
        );

        self::assertSame([
            'stock' => 10,
            'deliveryTime' => 3,
            'unit' => 'pcs',
            'warrantyMonths' => 24,
        ], $inventory->toArray());
    }
}
