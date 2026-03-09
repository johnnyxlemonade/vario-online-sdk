<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\Pricing;

use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\Common\VatRate;
use Lemonade\Vario\Domain\Product\Pricing\Price;
use Lemonade\Vario\Domain\Product\Pricing\PriceLevel;
use PHPUnit\Framework\TestCase;

final class PriceLevelTest extends TestCase
{
    public function testGetters(): void
    {
        $price = new Price(
            value: 150.0,
            includesVat: false,
            vatRate: VatRate::STANDARD,
            currency: Currency::CZK
        );

        $level = new PriceLevel(
            code: 'B2B',
            price: $price
        );

        self::assertSame('B2B', $level->getCode());
        self::assertSame($price, $level->getPrice());
    }

    public function testToArray(): void
    {
        $price = new Price(
            value: 150.0,
            includesVat: false,
            vatRate: VatRate::STANDARD,
            currency: Currency::CZK
        );

        $level = new PriceLevel(
            code: 'B2B',
            price: $price
        );

        self::assertSame([
            'code' => 'B2B',
            'price' => [
                'value' => 150.0,
                'includesVat' => false,
                'vatRate' => 21.0,
                'currency' => 'CZK',
            ],
        ], $level->toArray());
    }
}
