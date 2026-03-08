<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product;

use Lemonade\Vario\Domain\Product\Product;
use Lemonade\Vario\Domain\Product\ProductCollection;
use PHPUnit\Framework\TestCase;

final class ProductCollectionTest extends TestCase
{
    public function testCountAndFirst(): void
    {
        $p1 = new Product([]);
        $p2 = new Product([]);

        $collection = new ProductCollection($p1, $p2);

        self::assertSame(2, $collection->count());
        self::assertSame($p1, $collection->first());
    }

    public function testIsEmpty(): void
    {
        $collection = new ProductCollection();

        self::assertTrue($collection->isEmpty());
    }

    public function testFilter(): void
    {
        $p1 = new Product([]);
        $p2 = new Product([]);

        $collection = new ProductCollection($p1, $p2);

        $filtered = $collection->filter(fn(Product $p) => $p === $p2);

        self::assertSame(1, $filtered->count());
        self::assertSame($p2, $filtered->first());
    }

    public function testMap(): void
    {
        $p1 = new Product([]);
        $p2 = new Product([]);

        $collection = new ProductCollection($p1, $p2);

        $result = $collection->map(fn(Product $p) => spl_object_id($p));

        self::assertCount(2, $result);
    }

    public function testFind(): void
    {
        $p1 = new Product([]);
        $p2 = new Product([]);

        $collection = new ProductCollection($p1, $p2);

        $found = $collection->find(fn(Product $p) => $p === $p2);

        self::assertSame($p2, $found);
    }

    public function testIterator(): void
    {
        $p1 = new Product([]);
        $p2 = new Product([]);

        $collection = new ProductCollection($p1, $p2);

        $iterator = $collection->getIterator();

        self::assertInstanceOf(\ArrayIterator::class, $iterator);
        self::assertSame([$p1, $p2], iterator_to_array($iterator, false));
    }

    public function testFindReturnsNull(): void
    {
        $p1 = new Product([]);
        $p2 = new Product([]);

        $collection = new ProductCollection($p1);

        $found = $collection->find(fn(Product $p) => $p === $p2);

        self::assertNull($found);
    }

    public function testToArray(): void
    {
        $p1 = new Product([]);
        $p2 = new Product([]);

        $collection = new ProductCollection($p1, $p2);

        $array = $collection->toArray();

        self::assertSame([$p1, $p2], $array);
    }
}
