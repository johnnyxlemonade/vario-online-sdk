<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\Pricing;

use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\Common\VatRate;
use Lemonade\Vario\Domain\Product\Pricing\Price;
use Lemonade\Vario\Domain\Product\Pricing\PriceCollection;
use Lemonade\Vario\Domain\Product\Pricing\PriceLevel;
use Lemonade\Vario\Domain\Product\Pricing\ProductPrices;
use PHPUnit\Framework\TestCase;

final class ProductPricesTest extends TestCase
{
    public function testGetters(): void
    {
        $levels = new PriceCollection();

        $price = new Price(
            value: 199.9,
            includesVat: true,
            vatRate: VatRate::STANDARD,
            currency: Currency::CZK
        );

        $productPrices = new ProductPrices(
            levels: $levels,
            basePrice: $price
        );

        self::assertTrue($productPrices->hasBasePrice());
        self::assertSame($price, $productPrices->getBasePrice());
        self::assertSame($levels, $productPrices->getLevels());
    }

    public function testWithoutBasePrice(): void
    {
        $levels = new PriceCollection();

        $productPrices = new ProductPrices(
            levels: $levels
        );

        self::assertFalse($productPrices->hasBasePrice());
        self::assertNull($productPrices->getBasePrice());
        self::assertSame($levels, $productPrices->getLevels());
    }

    public function testToArray(): void
    {
        $levels = new PriceCollection();

        $levelPrice = new Price(
            value: 150.0,
            includesVat: false,
            vatRate: VatRate::STANDARD,
            currency: Currency::CZK
        );

        $levels->add(new PriceLevel(
            code: 'B2B',
            price: $levelPrice
        ));

        $basePrice = new Price(
            value: 199.9,
            includesVat: true,
            vatRate: VatRate::STANDARD,
            currency: Currency::CZK
        );

        $productPrices = new ProductPrices(
            levels: $levels,
            basePrice: $basePrice
        );

        $array = $productPrices->toArray();

        self::assertSame([
            'basePrice' => [
                'value' => 199.9,
                'includesVat' => true,
                'vatRate' => 21.0,
                'currency' => 'CZK',
            ],
            'levels' => [
                'B2B' => [
                    'code' => 'B2B',
                    'price' => [
                        'value' => 150.0,
                        'includesVat' => false,
                        'vatRate' => 21.0,
                        'currency' => 'CZK',
                    ],
                ],
            ],
        ], $array);
    }
}
