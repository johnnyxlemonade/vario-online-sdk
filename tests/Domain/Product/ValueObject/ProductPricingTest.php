<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\ValueObject;

use Lemonade\Vario\Domain\Common\VatRate;
use Lemonade\Vario\Domain\Product\Pricing\Price;
use Lemonade\Vario\Domain\Product\ValueObject\ProductPricing;
use PHPUnit\Framework\TestCase;

final class ProductPricingTest extends TestCase
{
    public function testGetters(): void
    {
        $price = new Price(
            value: 199.9,
            includesVat: true,
            vatRate: VatRate::STANDARD
        );

        $pricing = new ProductPricing($price);

        self::assertTrue($pricing->hasPrice());

        $result = $pricing->getPrice();

        self::assertInstanceOf(Price::class, $result);
        self::assertSame($price, $result);

        self::assertSame(199.9, $result->getValue());
        self::assertTrue($result->isVatIncluded());
        self::assertSame(21.0, $result->getVatPercentage());
    }

    public function testNullValues(): void
    {
        $pricing = new ProductPricing(null);

        self::assertNull($pricing->getPrice());
        self::assertFalse($pricing->hasPrice());
    }

    public function testToArray(): void
    {
        $price = new Price(
            value: 100.0,
            includesVat: false,
            vatRate: VatRate::STANDARD
        );

        $pricing = new ProductPricing($price);

        self::assertSame([
            'price' => [
                'value' => 100.0,
                'includesVat' => false,
                'vatRate' => 21.0,
                'currency' => null,
            ],
        ], $pricing->toArray());
    }
}
