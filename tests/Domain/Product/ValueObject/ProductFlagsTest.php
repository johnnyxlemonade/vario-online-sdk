<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\ValueObject;

use Lemonade\Vario\Domain\Product\ValueObject\ProductFlag;
use Lemonade\Vario\Domain\Product\ValueObject\ProductFlags;
use PHPUnit\Framework\TestCase;

final class ProductFlagsTest extends TestCase
{
    public function testFromFlags(): void
    {
        $flags = ProductFlags::fromFlags([
            ProductFlag::SALE,
            ProductFlag::NEW,
        ]);

        self::assertTrue($flags->isSale());
        self::assertTrue($flags->isNew());
        self::assertFalse($flags->isDiscount());
    }

    public function testHas(): void
    {
        $flags = ProductFlags::fromFlags([
            ProductFlag::DISCOUNT,
        ]);

        self::assertTrue($flags->has(ProductFlag::DISCOUNT));
        self::assertFalse($flags->has(ProductFlag::SALE));
    }

    public function testSetEnable(): void
    {
        $flags = ProductFlags::fromFlags([]);

        $flags->set(ProductFlag::SALE, true);

        self::assertTrue($flags->isSale());
    }

    public function testSetDisable(): void
    {
        $flags = ProductFlags::fromFlags([
            ProductFlag::SALE,
        ]);

        $flags->set(ProductFlag::SALE, false);

        self::assertFalse($flags->isSale());
    }

    public function testAll(): void
    {
        $flags = ProductFlags::fromFlags([
            ProductFlag::SALE,
            ProductFlag::NEW,
        ]);

        $all = $flags->all();

        self::assertCount(2, $all);
        self::assertContains(ProductFlag::SALE, $all);
        self::assertContains(ProductFlag::NEW, $all);
    }

    public function testToArray(): void
    {
        $flags = ProductFlags::fromFlags([
            ProductFlag::SALE,
            ProductFlag::RECOMMENDED,
        ]);

        self::assertSame([
            'sale' => true,
            'new' => false,
            'discount' => false,
            'clearance' => false,
            'recommended' => true,
            'preparing' => false,
        ], $flags->toArray());
    }
}
