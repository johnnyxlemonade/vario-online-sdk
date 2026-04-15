<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\ValueObject;

use Lemonade\Vario\Domain\Product\ValueObject\ProductClassification;
use PHPUnit\Framework\TestCase;

final class ProductClassificationTest extends TestCase
{
    public function testGetters(): void
    {
        $classification = new ProductClassification(
            categoryId: 'cat-1',
            categoryName: 'Electronics',
            brand: 'Acme',
            priceLevelCode: 'AAA'
        );

        self::assertSame('cat-1', $classification->getCategoryId());
        self::assertSame('Electronics', $classification->getCategoryName());
        self::assertSame('Acme', $classification->getBrand());
        self::assertSame('AAA', $classification->getPriceLevelCode());
    }

    public function testNullValues(): void
    {
        $classification = new ProductClassification(
            categoryId: null,
            categoryName: null,
            brand: null,
            priceLevelCode: null,
        );

        self::assertNull($classification->getCategoryId());
        self::assertNull($classification->getCategoryName());
        self::assertNull($classification->getBrand());
        self::assertNull($classification->getPriceLevelCode());
    }

    public function testToArray(): void
    {
        $classification = new ProductClassification(
            categoryId: 'cat-1',
            categoryName: 'Electronics',
            brand: 'Acme',
            priceLevelCode: 'AAA',
        );

        self::assertSame([
            'categoryId' => 'cat-1',
            'categoryName' => 'Electronics',
            'brand' => 'Acme',
            'priceLevelCode' => 'AAA',
        ], $classification->toArray());
    }
}
