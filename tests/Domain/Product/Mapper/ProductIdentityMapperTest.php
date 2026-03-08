<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapper\ProductIdentityMapper;
use Lemonade\Vario\Domain\Product\Mapping\ProductIdentityMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductIdentity;
use PHPUnit\Framework\TestCase;

final class ProductIdentityMapperTest extends TestCase
{
    public function testMapsIdentity(): void
    {
        $mapping = new ProductIdentityMapping(
            uuid: 'uuid',
            sku: 'sku',
            catalogNumber: 'catalog',
            name: 'name'
        );

        $row = new DatasetRow([
            'uuid' => 'abc-123',
            'sku' => 'SKU-1',
            'catalog' => 'CAT-1',
            'name' => 'Test Product',
        ]);

        $mapper = new ProductIdentityMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductIdentity::class, $result);
        self::assertSame('abc-123', $result->getUuid());
        self::assertSame('SKU-1', $result->getSku());
        self::assertSame('CAT-1', $result->getCatalogNumber());
        self::assertSame('Test Product', $result->getName());
    }

    public function testReturnsNullWhenUuidMissing(): void
    {
        $mapping = new ProductIdentityMapping(
            uuid: 'uuid',
            sku: 'sku',
            catalogNumber: 'catalog',
            name: 'name'
        );

        $row = new DatasetRow([]);

        $mapper = new ProductIdentityMapper($mapping);

        self::assertNull($mapper->map($row));
    }

    public function testMapsPartialIdentity(): void
    {
        $mapping = new ProductIdentityMapping(
            uuid: 'uuid',
            sku: 'sku',
            catalogNumber: 'catalog',
            name: 'name'
        );

        $row = new DatasetRow([
            'uuid' => 'abc-123',
        ]);

        $mapper = new ProductIdentityMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductIdentity::class, $result);
        self::assertSame('abc-123', $result->getUuid());
        self::assertNull($result->getSku());
        self::assertNull($result->getCatalogNumber());
        self::assertNull($result->getName());
    }
}
