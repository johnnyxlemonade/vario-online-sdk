<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\Common\VatRate;
use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapper\ProductPricesMapper;
use Lemonade\Vario\Domain\Product\Mapping\ProductPricesMapping;
use Lemonade\Vario\Domain\Product\Pricing\Price;
use Lemonade\Vario\Domain\Product\Pricing\ProductPrices;
use PHPUnit\Framework\TestCase;

final class ProductPricesMapperTest extends TestCase
{
    public function testMapsBasePrice(): void
    {
        $mapping = new ProductPricesMapping(
            basePrice: 'price',
            vatRate: 'vat',
            priceIncludesVat: 'includesVat',
            currency: 'currency'
        );

        $row = new DatasetRow([
            'price' => 199.9,
            'vat' => 'Základní',
            'includesVat' => 1,
            'currency' => 'CZK',
        ]);

        $mapper = new ProductPricesMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductPrices::class, $result);

        $price = $result->getBasePrice();

        if ($price === null) {
            self::fail('Base price should not be null.');
        }

        self::assertInstanceOf(Price::class, $price);

        self::assertSame(199.9, $price->getValue());
        self::assertTrue($price->isVatIncluded());
        self::assertSame(VatRate::STANDARD, $price->getVatRate());
        self::assertSame(Currency::CZK, $price->getCurrency());
    }

    public function testReturnsNullWhenPriceMissing(): void
    {
        $mapping = new ProductPricesMapping(
            basePrice: 'price'
        );

        $row = new DatasetRow([]);

        $mapper = new ProductPricesMapper($mapping);

        $result = $mapper->map($row);

        self::assertNull($result);
    }

    public function testMapsPriceWithoutOptionalFields(): void
    {
        $mapping = new ProductPricesMapping(
            basePrice: 'price'
        );

        $row = new DatasetRow([
            'price' => 100.0,
        ]);

        $mapper = new ProductPricesMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductPrices::class, $result);

        $price = $result->getBasePrice();

        if ($price === null) {
            self::fail('Base price should not be null.');
        }

        self::assertSame(100.0, $price->getValue());
        self::assertFalse($price->isVatIncluded());
        self::assertNull($price->getVatRate());
        self::assertNull($price->getCurrency());
    }

    public function testReturnsNullWhenBasePriceColumnNotConfigured(): void
    {
        $mapping = new ProductPricesMapping(
            basePrice: null
        );

        $row = new DatasetRow([
            'price' => 100,
        ]);

        $mapper = new ProductPricesMapper($mapping);

        $result = $mapper->map($row);

        self::assertNull($result);
    }

    public function testMapsPriceWithoutVatColumn(): void
    {
        $mapping = new ProductPricesMapping(
            basePrice: 'price',
            vatRate: null
        );

        $row = new DatasetRow([
            'price' => 100.0,
        ]);

        $mapper = new ProductPricesMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductPrices::class, $result);

        $price = $result->getBasePrice();

        self::assertNull($price?->getVatRate());
    }

    public function testMapsPriceWithoutIncludesVatColumn(): void
    {
        $mapping = new ProductPricesMapping(
            basePrice: 'price',
            priceIncludesVat: null
        );

        $row = new DatasetRow([
            'price' => 100.0,
        ]);

        $mapper = new ProductPricesMapper($mapping);

        $result = $mapper->map($row);

        $price = $result?->getBasePrice();

        self::assertFalse($price?->isVatIncluded());
    }

    public function testMapsPriceWithoutCurrencyColumn(): void
    {
        $mapping = new ProductPricesMapping(
            basePrice: 'price',
            currency: null
        );

        $row = new DatasetRow([
            'price' => 100.0,
        ]);

        $mapper = new ProductPricesMapper($mapping);

        $result = $mapper->map($row);

        $price = $result?->getBasePrice();

        self::assertNull($price?->getCurrency());
    }
}
