<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapper\ProductAttributesMapper;
use Lemonade\Vario\Domain\Product\Mapper\ProductFlagsMapper;
use Lemonade\Vario\Domain\Product\Mapper\ProductIdentityMapper;
use Lemonade\Vario\Domain\Product\Mapper\ProductInventoryMapper;
use Lemonade\Vario\Domain\Product\Mapper\ProductPricingMapper;
use Lemonade\Vario\Domain\Product\Mapping\ProductAttributesMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductFlagsMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductIdentityMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductInventoryMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductPricingMapping;
use Lemonade\Vario\Domain\Product\ProductDatasetMapping;
use Lemonade\Vario\Domain\Product\ProductMapper;
use Lemonade\Vario\Domain\Product\ValueObject\ProductAttributes;
use Lemonade\Vario\Domain\Product\ValueObject\ProductFlags;
use Lemonade\Vario\Domain\Product\ValueObject\ProductIdentity;
use Lemonade\Vario\Domain\Product\ValueObject\ProductInventory;
use Lemonade\Vario\Domain\Product\ValueObject\ProductPricing;
use PHPUnit\Framework\TestCase;

final class ProductMapperTest extends TestCase
{
    public function testMapsProduct(): void
    {
        $mapping = new ProductDatasetMapping();

        $mapping->register(
            new ProductIdentityMapper(
                new ProductIdentityMapping(
                    uuid: 'uuid',
                    sku: 'sku',
                    catalogNumber: null,
                    name: 'name'
                )
            )
        );

        $mapping->register(
            new ProductPricingMapper(
                new ProductPricingMapping(
                    price: 'price'
                )
            )
        );

        $mapping->register(
            new ProductInventoryMapper(
                new ProductInventoryMapping(
                    stock: 'stock'
                )
            )
        );

        $mapping->register(
            new ProductFlagsMapper(
                new ProductFlagsMapping(
                    sale: 'sale'
                )
            )
        );

        $row = new DatasetRow([
            'uuid' => 'abc-123',
            'sku' => 'SKU-1',
            'name' => 'Test product',
            'price' => 199.9,
            'stock' => 10,
            'sale' => 1,
        ]);

        $mapper = new ProductMapper($mapping);

        $product = $mapper->map($row);

        $identity = $product->get(ProductIdentity::class);
        self::assertInstanceOf(ProductIdentity::class, $identity);

        self::assertSame('abc-123', $identity->getUuid());
        self::assertSame('SKU-1', $identity->getSku());
        self::assertSame('Test product', $identity->getName());

        $pricing = $product->get(ProductPricing::class);
        self::assertInstanceOf(ProductPricing::class, $pricing);

        self::assertSame(199.9, $pricing->getPrice());

        $inventory = $product->get(ProductInventory::class);
        self::assertInstanceOf(ProductInventory::class, $inventory);

        self::assertSame(10, $inventory->getStock());

        $flags = $product->get(ProductFlags::class);
        self::assertInstanceOf(ProductFlags::class, $flags);

        self::assertTrue($flags->isSale());
    }

    public function testMapsNullValues(): void
    {
        $mapping = new ProductDatasetMapping();

        $mapping->register(
            new ProductIdentityMapper(
                new ProductIdentityMapping(
                    uuid: 'uuid',
                    sku: 'sku'
                )
            )
        );

        $row = new DatasetRow([
            'uuid' => 'abc-123',
            'sku' => null,
        ]);

        $mapper = new ProductMapper($mapping);

        $product = $mapper->map($row);

        $identity = $product->get(ProductIdentity::class);
        self::assertInstanceOf(ProductIdentity::class, $identity);

        self::assertSame('abc-123', $identity->getUuid());
        self::assertNull($identity->getSku());
    }

    public function testFiltersNonScalarAttributes(): void
    {
        $mapping = new ProductDatasetMapping();

        $mapping->register(
            new ProductIdentityMapper(
                new ProductIdentityMapping(uuid: 'uuid')
            )
        );

        $mapping->register(
            new ProductAttributesMapper(
                new ProductAttributesMapping([
                    'color' => 'color',
                    'invalid' => 'invalid',
                ])
            )
        );

        $row = new DatasetRow([
            'uuid' => '1',
            'color' => 'red',
            'invalid' => ['array'],
        ]);

        $mapper = new ProductMapper($mapping);

        $product = $mapper->map($row);

        $attributes = $product->get(ProductAttributes::class);

        self::assertInstanceOf(ProductAttributes::class, $attributes);
        self::assertSame('red', $attributes->get('color'));
        self::assertFalse($attributes->has('invalid'));
    }
}
