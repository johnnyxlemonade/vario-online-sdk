<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapper\AbstractProductSectionMapper;
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
use Lemonade\Vario\Domain\Product\ValueObject\ProductSection;
use PHPUnit\Framework\TestCase;

final class ProductMapperTest extends TestCase
{
    public function testMapsProduct(): void
    {
        $mapping = (new ProductDatasetMapping())
            ->add(
                new ProductIdentityMapper(
                    new ProductIdentityMapping(
                        uuid: 'uuid',
                        sku: 'sku',
                        catalogNumber: null,
                        name: 'name'
                    )
                )
            )
            ->add(
                new ProductPricingMapper(
                    new ProductPricingMapping(
                        price: 'price'
                    )
                )
            )
            ->add(
                new ProductInventoryMapper(
                    new ProductInventoryMapping(
                        stock: 'stock'
                    )
                )
            )
            ->add(
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

        $identity = $product->identity();
        self::assertInstanceOf(ProductIdentity::class, $identity);

        self::assertSame('abc-123', $identity->getUuid());
        self::assertSame('SKU-1', $identity->getSku());
        self::assertSame('Test product', $identity->getName());

        $pricing = $product->pricing();
        self::assertInstanceOf(ProductPricing::class, $pricing);

        self::assertSame(199.9, $pricing->getPrice());

        $inventory = $product->inventory();
        self::assertInstanceOf(ProductInventory::class, $inventory);

        self::assertSame(10, $inventory->getStock());

        $flags = $product->flags();
        self::assertInstanceOf(ProductFlags::class, $flags);

        self::assertTrue($flags->isSale());
    }

    public function testMapsNullValues(): void
    {
        $mapping = (new ProductDatasetMapping())
            ->add(
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

        $identity = $product->identity();
        self::assertInstanceOf(ProductIdentity::class, $identity);

        self::assertSame('abc-123', $identity->getUuid());
        self::assertNull($identity->getSku());
    }

    public function testFiltersNonScalarAttributes(): void
    {
        $mapping = (new ProductDatasetMapping())
            ->add(
                new ProductIdentityMapper(
                    new ProductIdentityMapping(uuid: 'uuid')
                )
            )
            ->add(
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

        $attributes = $product->attributes();

        self::assertInstanceOf(ProductAttributes::class, $attributes);
        self::assertSame('red', $attributes->get('color'));
        self::assertFalse($attributes->has('invalid'));
    }

    public function testSupportsCustomSectionMapper(): void
    {
        $mapping = (new ProductDatasetMapping())
            ->add(
                new ProductIdentityMapper(
                    new ProductIdentityMapping(uuid: 'uuid')
                )
            )
            ->add(
                new class extends AbstractProductSectionMapper {
                    public function map(DatasetRow $row): ?ProductSection
                    {
                        $value = $this->mapString($row, 'custom');

                        if ($value === null) {
                            return null;
                        }

                        return new class ($value) implements ProductSection {
                            public function __construct(
                                private readonly string $value
                            ) {}

                            public function getValue(): string
                            {
                                return $this->value;
                            }

                            public function toArray(): array
                            {
                                return ['value' => $this->value];
                            }
                        };
                    }

                }
            );

        $row = new DatasetRow([
            'uuid' => '1',
            'custom' => 'hello',
        ]);

        $mapper = new ProductMapper($mapping);

        $product = $mapper->map($row);

        $sections = $product->all();

        self::assertCount(2, $sections);
    }

    public function testIterate(): void
    {
        $mapping = (new ProductDatasetMapping())
            ->add(
                new ProductIdentityMapper(
                    new ProductIdentityMapping(uuid: 'uuid')
                )
            );

        $rows = [
            ['uuid' => '1'],
            ['uuid' => '2'],
        ];

        $mapper = new ProductMapper($mapping);

        $result = iterator_to_array($mapper->iterate($rows), false);

        self::assertCount(2, $result);
        self::assertSame('1', $result[0]->identity()?->getUuid());
        self::assertSame('2', $result[1]->identity()?->getUuid());
    }

    public function testCollect(): void
    {
        $mapping = (new ProductDatasetMapping())
            ->add(
                new ProductIdentityMapper(
                    new ProductIdentityMapping(uuid: 'uuid')
                )
            );

        $rows = [
            ['uuid' => '1'],
            ['uuid' => '2'],
        ];

        $mapper = new ProductMapper($mapping);

        $collection = $mapper->collect($rows);

        self::assertSame(2, $collection->count());
    }

    public function testLazy(): void
    {
        $mapping = (new ProductDatasetMapping())
            ->add(
                new ProductIdentityMapper(
                    new ProductIdentityMapping(uuid: 'uuid')
                )
            );

        $rows = [
            ['uuid' => '1'],
            ['uuid' => '2'],
        ];

        $mapper = new ProductMapper($mapping);

        $lazy = $mapper->lazy($rows);

        $items = iterator_to_array($lazy);

        self::assertCount(2, $items);
    }
}
