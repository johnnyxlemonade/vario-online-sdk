<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapper\ProductPricingMapper;
use Lemonade\Vario\Domain\Product\Mapping\ProductPricingMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductPricing;
use PHPUnit\Framework\TestCase;

final class ProductPricingMapperTest extends TestCase
{
    public function testMapsPricing(): void
    {
        $mapping = new ProductPricingMapping(
            price: 'price',
            vatRate: 'vat',
            priceIncludesVat: 'includesVat'
        );

        $row = new DatasetRow([
            'price' => 199.9,
            'vat' => 'Základní',
            'includesVat' => 1,
        ]);

        $mapper = new ProductPricingMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductPricing::class, $result);

        $price = $result->getPrice();

        self::assertNotNull($price);
        self::assertSame(199.9, $price->getValue());
        self::assertTrue($price->isVatIncluded());
        self::assertNotNull($price->getVatRate());
        self::assertSame(21.0, $price->getVatPercentage());
    }

    public function testReturnsNullWhenAllFieldsMissing(): void
    {
        $mapping = new ProductPricingMapping(
            price: 'price',
            vatRate: 'vat',
            priceIncludesVat: 'includesVat'
        );

        $row = new DatasetRow([]);

        $mapper = new ProductPricingMapper($mapping);

        self::assertNull($mapper->map($row));
    }

    public function testMapsPartialPricing(): void
    {
        $mapping = new ProductPricingMapping(
            price: 'price',
            vatRate: 'vat',
            priceIncludesVat: 'includesVat'
        );

        $row = new DatasetRow([
            'price' => 100,
        ]);

        $mapper = new ProductPricingMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductPricing::class, $result);

        $price = $result->getPrice();

        self::assertNotNull($price);
        self::assertSame(100.0, $price->getValue());
        self::assertFalse($price->isVatIncluded());
        self::assertNull($price->getVatRate());
    }
}
