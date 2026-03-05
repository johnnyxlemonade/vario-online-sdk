<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product;

use Lemonade\Vario\Domain\Product\ValueObject\ProductFlag;
use Lemonade\Vario\Domain\Product\ValueObject\ProductFlags;
use PHPUnit\Framework\TestCase;

final class ProductFlagsTest extends TestCase
{
    public function testCreatesFlagsFromList(): void
    {
        $flags = ProductFlags::fromFlags([
            ProductFlag::SALE,
            ProductFlag::NEW,
        ]);

        self::assertTrue($flags->isSale());
        self::assertTrue($flags->isNew());
        self::assertFalse($flags->isDiscount());
    }

    public function testSetAndUnsetFlag(): void
    {
        $flags = ProductFlags::fromFlags([]);

        $flags->set(ProductFlag::SALE, true);

        self::assertTrue($flags->isSale());

        $flags->set(ProductFlag::SALE, false);

        self::assertFalse($flags->isSale());
    }

    public function testReturnsAllFlags(): void
    {
        $flags = ProductFlags::fromFlags([
            ProductFlag::SALE,
            ProductFlag::DISCOUNT,
        ]);

        $all = $flags->all();

        self::assertCount(2, $all);
        self::assertContains(ProductFlag::SALE, $all);
        self::assertContains(ProductFlag::DISCOUNT, $all);
    }
}
