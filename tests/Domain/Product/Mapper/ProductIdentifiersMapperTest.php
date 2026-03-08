<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapper\ProductIdentifiersMapper;
use Lemonade\Vario\Domain\Product\Mapping\ProductIdentifiersMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductIdentifiers;
use PHPUnit\Framework\TestCase;

final class ProductIdentifiersMapperTest extends TestCase
{
    public function testMapsIdentifiers(): void
    {
        $mapping = new ProductIdentifiersMapping(
            ean: 'ean',
            mpn: 'mpn',
            supplierCode: 'supplier'
        );

        $row = new DatasetRow([
            'ean' => '1234567890123',
            'mpn' => 'MPN-1',
            'supplier' => 'SUP-42',
        ]);

        $mapper = new ProductIdentifiersMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductIdentifiers::class, $result);
        self::assertSame('1234567890123', $result->getEan());
        self::assertSame('MPN-1', $result->getMpn());
        self::assertSame('SUP-42', $result->getSupplierCode());
    }

    public function testReturnsNullWhenAllFieldsMissing(): void
    {
        $mapping = new ProductIdentifiersMapping(
            ean: 'ean',
            mpn: 'mpn',
            supplierCode: 'supplier'
        );

        $row = new DatasetRow([]);

        $mapper = new ProductIdentifiersMapper($mapping);

        self::assertNull($mapper->map($row));
    }

    public function testMapsPartialIdentifiers(): void
    {
        $mapping = new ProductIdentifiersMapping(
            ean: 'ean',
            mpn: 'mpn',
            supplierCode: 'supplier'
        );

        $row = new DatasetRow([
            'ean' => '1234567890123',
        ]);

        $mapper = new ProductIdentifiersMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductIdentifiers::class, $result);
        self::assertSame('1234567890123', $result->getEan());
        self::assertNull($result->getMpn());
        self::assertNull($result->getSupplierCode());
    }
}
