<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product;

use Lemonade\Vario\Domain\Product\LazyProductCollection;
use Lemonade\Vario\Domain\Product\Product;
use PHPUnit\Framework\TestCase;

final class LazyProductCollectionTest extends TestCase
{
    public function testIteration(): void
    {
        $p1 = new Product([]);
        $p2 = new Product([]);

        $collection = new LazyProductCollection(fn() => [$p1, $p2]);

        $items = [];

        foreach ($collection as $item) {
            $items[] = $item;
        }

        self::assertCount(2, $items);
    }

    public function testFilter(): void
    {
        $p1 = new Product([]);
        $p2 = new Product([]);

        $collection = new LazyProductCollection(fn() => [$p1, $p2]);

        $filtered = $collection->filter(fn(Product $p) => $p === $p2);

        $items = iterator_to_array($filtered);

        self::assertCount(1, $items);
        self::assertSame($p2, $items[0]);
    }

    public function testFirst(): void
    {
        $p1 = new Product([]);
        $p2 = new Product([]);

        $collection = new LazyProductCollection(fn() => [$p1, $p2]);

        self::assertSame($p1, $collection->first());
    }

    public function testCollect(): void
    {
        $p1 = new Product([]);
        $p2 = new Product([]);

        $collection = new LazyProductCollection(fn() => [$p1, $p2]);

        $materialized = $collection->collect();

        self::assertSame(2, $materialized->count());
    }
}
