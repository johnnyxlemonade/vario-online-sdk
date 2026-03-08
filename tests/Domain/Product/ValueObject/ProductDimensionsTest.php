<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\ValueObject;

use Lemonade\Vario\Domain\Product\ValueObject\ProductDimensions;
use PHPUnit\Framework\TestCase;

final class ProductDimensionsTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $dimensions = new ProductDimensions(
            width: 10.5,
            height: 20.0,
            depth: 5.5,
            weightGrams: 1500
        );

        self::assertSame(10.5, $dimensions->getWidth());
        self::assertSame(20.0, $dimensions->getHeight());
        self::assertSame(5.5, $dimensions->getDepth());
        self::assertSame(1500.0, $dimensions->getWeightGrams());
    }

    public function testFromKgConvertsToGrams(): void
    {
        $dimensions = ProductDimensions::fromKg(
            width: 10,
            height: 20,
            depth: 30,
            weightKg: 1.5
        );

        self::assertSame(1500.0, $dimensions->getWeightGrams());
    }

    public function testFromKgWithNullWeight(): void
    {
        $dimensions = ProductDimensions::fromKg(
            width: 10,
            height: 20,
            depth: 30,
            weightKg: null
        );

        self::assertNull($dimensions->getWeightGrams());
    }

    public function testNullValues(): void
    {
        $dimensions = new ProductDimensions(
            width: null,
            height: null,
            depth: null,
            weightGrams: null
        );

        self::assertNull($dimensions->getWidth());
        self::assertNull($dimensions->getHeight());
        self::assertNull($dimensions->getDepth());
        self::assertNull($dimensions->getWeightGrams());
    }

    public function testToArray(): void
    {
        $dimensions = new ProductDimensions(
            width: 1,
            height: 2,
            depth: 3,
            weightGrams: 400
        );

        self::assertSame([
            'width' => 1.0,
            'height' => 2.0,
            'depth' => 3.0,
            'weightGrams' => 400.0,
        ], $dimensions->toArray());
    }
}
