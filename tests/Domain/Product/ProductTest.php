<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product;

use Lemonade\Vario\Domain\Common\VatRate;
use Lemonade\Vario\Domain\Product\Pricing\Price;
use Lemonade\Vario\Domain\Product\Pricing\PriceCollection;
use Lemonade\Vario\Domain\Product\Pricing\ProductPrices;
use Lemonade\Vario\Domain\Product\Product;
use Lemonade\Vario\Domain\Product\ValueObject\ProductAttributes;
use Lemonade\Vario\Domain\Product\ValueObject\ProductClassification;
use Lemonade\Vario\Domain\Product\ValueObject\ProductDescription;
use Lemonade\Vario\Domain\Product\ValueObject\ProductDimensions;
use Lemonade\Vario\Domain\Product\ValueObject\ProductFlags;
use Lemonade\Vario\Domain\Product\ValueObject\ProductIdentifiers;
use Lemonade\Vario\Domain\Product\ValueObject\ProductIdentity;
use Lemonade\Vario\Domain\Product\ValueObject\ProductInventory;
use Lemonade\Vario\Domain\Product\ValueObject\ProductPricing;
use PHPUnit\Framework\TestCase;

final class ProductTest extends TestCase
{
    public function testGetAndHas(): void
    {
        $identity = new ProductIdentity('uuid-1', 'sku', 'cat', 'name');
        $product = new Product([$identity]);

        self::assertTrue($product->has(ProductIdentity::class));
        self::assertFalse($product->has(ProductDescription::class));

        self::assertSame($identity, $product->get(ProductIdentity::class));
        self::assertNull($product->get(ProductDescription::class));
    }

    public function testAll(): void
    {
        $identity = new ProductIdentity('uuid-1', 'sku', 'cat', 'name');
        $flags = new ProductFlags();

        $product = new Product([$identity, $flags]);

        $all = $product->all();

        self::assertCount(2, $all);
        self::assertSame($identity, $all[ProductIdentity::class]);
        self::assertSame($flags, $all[ProductFlags::class]);
    }

    public function testIdentityHelper(): void
    {
        $identity = new ProductIdentity('uuid-1', 'sku', 'cat', 'name');
        $product = new Product([$identity]);

        self::assertSame($identity, $product->identity());
    }

    public function testDescriptionHelper(): void
    {
        $description = new ProductDescription('short', 'long');
        $product = new Product([$description]);

        self::assertSame($description, $product->description());
    }

    public function testFlagsHelper(): void
    {
        $flags = new ProductFlags();
        $product = new Product([$flags]);

        self::assertSame($flags, $product->flags());
    }

    public function testDimensionsHelper(): void
    {
        $dimensions = new ProductDimensions(1.0, 2.0, 3.0, 400.0);
        $product = new Product([$dimensions]);

        self::assertSame($dimensions, $product->dimensions());
    }

    public function testPricingHelper(): void
    {
        $price = new Price(
            value: 100.0,
            includesVat: true,
            vatRate: VatRate::STANDARD
        );

        $pricing = new ProductPricing($price);

        $product = new Product([$pricing]);

        self::assertSame($pricing, $product->pricing());
    }

    public function testInventoryHelper(): void
    {
        $inventory = new ProductInventory(10, 3, 'pcs', 24);
        $product = new Product([$inventory]);

        self::assertSame($inventory, $product->inventory());
    }

    public function testIdentifiersHelper(): void
    {
        $identifiers = new ProductIdentifiers('ean-1', 'mpn-1', 'supplier-1');
        $product = new Product([$identifiers]);

        self::assertSame($identifiers, $product->identifiers());
    }

    public function testClassificationHelper(): void
    {
        $classification = new ProductClassification('cat-1', 'Category', 'Brand');
        $product = new Product([$classification]);

        self::assertSame($classification, $product->classification());
    }

    public function testAttributesHelper(): void
    {
        $attributes = new ProductAttributes(['color' => 'red']);
        $product = new Product([$attributes]);

        self::assertSame($attributes, $product->attributes());
    }

    public function testPricesHelper(): void
    {
        $prices = new ProductPrices(new PriceCollection());

        $product = new Product([$prices]);

        self::assertSame($prices, $product->prices());
    }

}
