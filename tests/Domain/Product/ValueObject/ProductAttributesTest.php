<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\ValueObject;

use Lemonade\Vario\Domain\Product\ValueObject\ProductAttributes;
use PHPUnit\Framework\TestCase;

final class ProductAttributesTest extends TestCase
{
    public function testAllReturnsAttributes(): void
    {
        $attributes = new ProductAttributes([
            'color' => 'red',
            'size' => 'L',
        ]);

        self::assertSame([
            'color' => 'red',
            'size' => 'L',
        ], $attributes->all());
    }

    public function testGet(): void
    {
        $attributes = new ProductAttributes([
            'color' => 'red',
            'stock' => 10,
        ]);

        self::assertSame('red', $attributes->get('color'));
        self::assertSame(10, $attributes->get('stock'));
        self::assertNull($attributes->get('missing'));
    }

    public function testHas(): void
    {
        $attributes = new ProductAttributes([
            'color' => 'red',
            'nullable' => null,
        ]);

        self::assertTrue($attributes->has('color'));
        self::assertTrue($attributes->has('nullable'));
        self::assertFalse($attributes->has('missing'));
    }

    public function testToArray(): void
    {
        $data = [
            'color' => 'red',
            'size' => 'M',
        ];

        $attributes = new ProductAttributes($data);

        self::assertSame($data, $attributes->toArray());
    }
}
