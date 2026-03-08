<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\ValueObject;

use Lemonade\Vario\Domain\Product\ValueObject\ProductIdentifiers;
use PHPUnit\Framework\TestCase;

final class ProductIdentifiersTest extends TestCase
{
    public function testGetters(): void
    {
        $identifiers = new ProductIdentifiers(
            ean: '1234567890123',
            mpn: 'MPN-001',
            supplierCode: 'SUP-42'
        );

        self::assertSame('1234567890123', $identifiers->getEan());
        self::assertSame('MPN-001', $identifiers->getMpn());
        self::assertSame('SUP-42', $identifiers->getSupplierCode());
    }

    public function testNullValues(): void
    {
        $identifiers = new ProductIdentifiers(
            ean: null,
            mpn: null,
            supplierCode: null
        );

        self::assertNull($identifiers->getEan());
        self::assertNull($identifiers->getMpn());
        self::assertNull($identifiers->getSupplierCode());
    }

    public function testToArray(): void
    {
        $identifiers = new ProductIdentifiers(
            ean: '1234567890123',
            mpn: 'MPN-001',
            supplierCode: 'SUP-42'
        );

        self::assertSame([
            'ean' => '1234567890123',
            'mpn' => 'MPN-001',
            'supplierCode' => 'SUP-42',
        ], $identifiers->toArray());
    }
}
