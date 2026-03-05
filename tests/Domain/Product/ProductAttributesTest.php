<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product;

use Lemonade\Vario\Domain\Product\ValueObject\ProductAttributes;
use PHPUnit\Framework\TestCase;

final class ProductAttributesTest extends TestCase
{
    public function testReturnsAllAttributes(): void
    {
        $attributes = new ProductAttributes([
            'color' => 'red',
            'size' => 'XL',
        ]);

        $all = $attributes->all();

        self::assertSame([
            'color' => 'red',
            'size' => 'XL',
        ], $all);
    }

    public function testGetAttribute(): void
    {
        $attributes = new ProductAttributes([
            'color' => 'black',
        ]);

        self::assertSame('black', $attributes->get('color'));
        self::assertNull($attributes->get('size'));
    }

    public function testHasAttribute(): void
    {
        $attributes = new ProductAttributes([
            'material' => 'cotton',
        ]);

        self::assertTrue($attributes->has('material'));
        self::assertFalse($attributes->has('color'));
    }
}
