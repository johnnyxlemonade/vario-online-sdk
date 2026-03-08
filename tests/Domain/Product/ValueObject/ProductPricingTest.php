<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\ValueObject;

use Lemonade\Vario\Domain\Product\ValueObject\ProductPricing;
use PHPUnit\Framework\TestCase;

final class ProductPricingTest extends TestCase
{
    public function testGetters(): void
    {
        $pricing = new ProductPricing(
            price: 199.9,
            vatRate: '21',
            priceIncludesVat: true
        );

        self::assertSame(199.9, $pricing->getPrice());
        self::assertSame('21', $pricing->getVatRate());
        self::assertTrue($pricing->isPriceIncludesVat());
    }

    public function testNullValues(): void
    {
        $pricing = new ProductPricing(
            price: null,
            vatRate: null,
            priceIncludesVat: null
        );

        self::assertNull($pricing->getPrice());
        self::assertNull($pricing->getVatRate());
        self::assertNull($pricing->isPriceIncludesVat());
    }

    public function testToArray(): void
    {
        $pricing = new ProductPricing(
            price: 100.0,
            vatRate: '21',
            priceIncludesVat: false
        );

        self::assertSame([
            'price' => 100.0,
            'vatRate' => '21',
            'priceIncludesVat' => false,
        ], $pricing->toArray());
    }
}
