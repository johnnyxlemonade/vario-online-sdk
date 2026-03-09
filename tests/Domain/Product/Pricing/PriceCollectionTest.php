<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\Pricing;

use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\Common\VatRate;
use Lemonade\Vario\Domain\Product\Pricing\Price;
use Lemonade\Vario\Domain\Product\Pricing\PriceCollection;
use Lemonade\Vario\Domain\Product\Pricing\PriceLevel;
use PHPUnit\Framework\TestCase;

final class PriceCollectionTest extends TestCase
{
    public function testAddAndGet(): void
    {
        $collection = new PriceCollection();

        $price = new Price(
            value: 100.0,
            includesVat: false,
            vatRate: VatRate::STANDARD,
            currency: Currency::CZK
        );

        $level = new PriceLevel('B2B', $price);

        $collection->add($level);

        $result = $collection->get('B2B');

        self::assertInstanceOf(PriceLevel::class, $result);
        self::assertSame($level, $result);
    }

    public function testGetReturnsNullWhenMissing(): void
    {
        $collection = new PriceCollection();

        self::assertNull($collection->get('UNKNOWN'));
    }

    public function testIsEmpty(): void
    {
        $collection = new PriceCollection();

        self::assertTrue($collection->isEmpty());

        $price = new Price(
            value: 50.0,
            includesVat: true,
            vatRate: VatRate::STANDARD,
            currency: Currency::CZK
        );

        $collection->add(new PriceLevel('A', $price));

        self::assertFalse($collection->isEmpty());
    }

    public function testCount(): void
    {
        $collection = new PriceCollection();

        self::assertSame(0, $collection->count());

        $price = new Price(
            value: 10.0,
            includesVat: false,
            vatRate: VatRate::STANDARD
        );

        $collection->add(new PriceLevel('A', $price));
        $collection->add(new PriceLevel('B', $price));

        self::assertSame(2, $collection->count());
    }

    public function testAll(): void
    {
        $collection = new PriceCollection();

        $price = new Price(
            value: 10.0,
            includesVat: false,
            vatRate: VatRate::STANDARD
        );

        $level = new PriceLevel('A', $price);

        $collection->add($level);

        $all = $collection->all();

        self::assertArrayHasKey('A', $all);
        self::assertSame($level, $all['A']);
    }

    public function testIterator(): void
    {
        $collection = new PriceCollection();

        $price = new Price(
            value: 10.0,
            includesVat: false,
            vatRate: VatRate::STANDARD
        );

        $collection->add(new PriceLevel('A', $price));
        $collection->add(new PriceLevel('B', $price));

        $items = [];

        foreach ($collection as $code => $level) {
            $items[$code] = $level;
        }

        self::assertCount(2, $items);
        self::assertArrayHasKey('A', $items);
        self::assertArrayHasKey('B', $items);
    }

    public function testToArray(): void
    {
        $collection = new PriceCollection();

        $price = new Price(
            value: 100.0,
            includesVat: false,
            vatRate: VatRate::STANDARD,
            currency: Currency::CZK
        );

        $collection->add(new PriceLevel('B2B', $price));

        $array = $collection->toArray();

        self::assertSame([
            'B2B' => [
                'code' => 'B2B',
                'price' => [
                    'value' => 100.0,
                    'includesVat' => false,
                    'vatRate' => 21.0,
                    'currency' => 'CZK',
                ],
            ],
        ], $array);
    }
}
