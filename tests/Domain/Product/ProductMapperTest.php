<?php declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapping\ProductAttributesMapping;
use Lemonade\Vario\Domain\Product\ProductDatasetMapping;
use Lemonade\Vario\Domain\Product\ProductMapper;
use Lemonade\Vario\Domain\Product\Mapping\ProductIdentityMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductPricingMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductInventoryMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductFlagsMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductAttributes;
use PHPUnit\Framework\TestCase;

final class ProductMapperTest extends TestCase
{
    public function testMapsProduct(): void
    {
        $mapping = new ProductDatasetMapping(
            identity: new ProductIdentityMapping(
                uuid: 'uuid',
                sku: 'sku',
                catalogNumber: null,
                name: 'name'
            ),
            pricing: new ProductPricingMapping(
                price: 'price',
                vatRate: null,
                priceIncludesVat: null
            ),
            inventory: new ProductInventoryMapping(
                stock: 'stock',
                deliveryTime: null,
                unit: null,
                warrantyMonths: null
            ),
            flags: new ProductFlagsMapping(
                sale: 'sale'
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

        self::assertSame('abc-123', $product->getIdentity()->getUuid());
        self::assertSame('SKU-1', $product->getIdentity()->getSku());
        self::assertSame('Test product', $product->getIdentity()->getName());

        self::assertSame(199.9, $product->getPricing()?->getPrice());
        self::assertSame(10, $product->getInventory()?->getStock());

        $flags = $product->getFlags();
        self::assertNotNull($flags);
        self::assertTrue($flags->isSale());
    }

    public function testThrowsWhenRequiredColumnMissing(): void
    {
        $mapping = new ProductDatasetMapping(
            identity: new ProductIdentityMapping(
                uuid: 'uuid',
                sku: null,
                catalogNumber: null,
                name: null
            )
        );

        $row = new DatasetRow([]);

        $mapper = new ProductMapper($mapping);

        $this->expectException(\RuntimeException::class);

        $mapper->map($row);
    }

    public function testMapsNullValues(): void
    {
        $mapping = new ProductDatasetMapping(
            identity: new ProductIdentityMapping(
                uuid: 'uuid',
                sku: 'sku',
                catalogNumber: null,
                name: null
            )
        );

        $row = new DatasetRow([
            'uuid' => 'abc-123',
            'sku' => null,
        ]);

        $mapper = new ProductMapper($mapping);

        $product = $mapper->map($row);

        self::assertSame('abc-123', $product->getIdentity()->getUuid());
        self::assertNull($product->getIdentity()->getSku());
    }

    public function testFiltersNonScalarAttributes(): void
    {
        $mapping = new ProductDatasetMapping(
            identity: new ProductIdentityMapping(uuid: 'uuid'),
            attributes: new ProductAttributesMapping([
                'color' => 'color',
                'invalid' => 'invalid',
            ])
        );

        $row = new DatasetRow([
            'uuid' => '1',
            'color' => 'red',
            'invalid' => ['array'], // nebude scalar
        ]);

        $mapper = new ProductMapper($mapping);

        $product = $mapper->map($row);

        $attributes = $product->getAttributes();

        self::assertInstanceOf(ProductAttributes::class, $attributes);

        self::assertSame('red', $attributes->get('color'));
        self::assertFalse($attributes->has('invalid'));
    }
}
