<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapper\ProductFlagsMapper;
use Lemonade\Vario\Domain\Product\Mapping\ProductFlagsMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductFlags;
use PHPUnit\Framework\TestCase;

final class ProductFlagsMapperTest extends TestCase
{
    public function testMapsFlags(): void
    {
        $mapping = new ProductFlagsMapping(
            sale: 'sale',
            new: 'new',
            discount: 'discount',
            clearance: 'clearance',
            recommended: 'recommended',
            preparing: 'preparing'
        );

        $row = new DatasetRow([
            'sale' => 1,
            'recommended' => true,
        ]);

        $mapper = new ProductFlagsMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductFlags::class, $result);
        self::assertTrue($result->isSale());
        self::assertTrue($result->isRecommended());
        self::assertFalse($result->isNew());
    }

    public function testReturnsNullWhenNoFlagsEnabled(): void
    {
        $mapping = new ProductFlagsMapping(
            sale: 'sale',
            new: 'new',
            discount: 'discount',
            clearance: 'clearance',
            recommended: 'recommended',
            preparing: 'preparing'
        );

        $row = new DatasetRow([]);

        $mapper = new ProductFlagsMapper($mapping);

        self::assertNull($mapper->map($row));
    }
}
