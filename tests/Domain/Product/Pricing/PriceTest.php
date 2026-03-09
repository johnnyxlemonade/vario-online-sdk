<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\Pricing;

use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\Common\VatRate;
use Lemonade\Vario\Domain\Product\Pricing\Price;
use PHPUnit\Framework\TestCase;

final class PriceTest extends TestCase
{
    public function testGetters(): void
    {
        $price = new Price(
            value: 199.9,
            includesVat: true,
            vatRate: VatRate::STANDARD,
            currency: Currency::CZK
        );

        self::assertSame(199.9, $price->getValue());
        self::assertTrue($price->isVatIncluded());
        self::assertTrue($price->isGross());

        self::assertSame(VatRate::STANDARD, $price->getVatRate());
        self::assertSame(21.0, $price->getVatPercentage());

        self::assertSame(Currency::CZK, $price->getCurrency());
    }

    public function testWithoutOptionalValues(): void
    {
        $price = new Price(
            value: 100.0,
            includesVat: false
        );

        self::assertSame(100.0, $price->getValue());
        self::assertFalse($price->isVatIncluded());
        self::assertFalse($price->isGross());

        self::assertNull($price->getVatRate());
        self::assertNull($price->getVatPercentage());
        self::assertNull($price->getCurrency());
    }

    public function testToArray(): void
    {
        $price = new Price(
            value: 150.0,
            includesVat: false,
            vatRate: VatRate::STANDARD,
            currency: Currency::EUR
        );

        self::assertSame([
            'value' => 150.0,
            'includesVat' => false,
            'vatRate' => 21.0,
            'currency' => 'EUR',
        ], $price->toArray());
    }
}
