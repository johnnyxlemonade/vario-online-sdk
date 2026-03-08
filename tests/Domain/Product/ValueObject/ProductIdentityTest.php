<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\ValueObject;

use Lemonade\Vario\Domain\Product\ValueObject\ProductIdentity;
use PHPUnit\Framework\TestCase;

final class ProductIdentityTest extends TestCase
{
    public function testGetters(): void
    {
        $identity = new ProductIdentity(
            uuid: 'abc-123',
            sku: 'SKU-1',
            catalogNumber: 'CAT-1',
            name: 'Test Product'
        );

        self::assertSame('abc-123', $identity->getUuid());
        self::assertSame('SKU-1', $identity->getSku());
        self::assertSame('CAT-1', $identity->getCatalogNumber());
        self::assertSame('Test Product', $identity->getName());
    }

    public function testNullValues(): void
    {
        $identity = new ProductIdentity(
            uuid: 'abc-123',
            sku: null,
            catalogNumber: null,
            name: null
        );

        self::assertSame('abc-123', $identity->getUuid());
        self::assertNull($identity->getSku());
        self::assertNull($identity->getCatalogNumber());
        self::assertNull($identity->getName());
    }

    public function testToArray(): void
    {
        $identity = new ProductIdentity(
            uuid: 'abc-123',
            sku: 'SKU-1',
            catalogNumber: 'CAT-1',
            name: 'Test Product'
        );

        self::assertSame([
            'uuid' => 'abc-123',
            'sku' => 'SKU-1',
            'catalogNumber' => 'CAT-1',
            'name' => 'Test Product',
        ], $identity->toArray());
    }
}
