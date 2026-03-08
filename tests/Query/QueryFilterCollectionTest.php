<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Query;

use Lemonade\Vario\Query\QueryFilterCollection;
use Lemonade\Vario\Query\QueryFilters;
use PHPUnit\Framework\TestCase;

final class QueryFilterCollectionTest extends TestCase
{
    public function test_empty_collection_is_empty(): void
    {
        $collection = QueryFilterCollection::empty();

        self::assertTrue($collection->isEmpty());
        self::assertSame([], $collection->all());
        self::assertSame([], $collection->toArray());
    }

    public function test_from_creates_collection_with_filters(): void
    {
        $filter1 = QueryFilters::equals('Name', 'John');
        $filter2 = QueryFilters::equals('Age', 30);

        $collection = QueryFilterCollection::from($filter1, $filter2);

        self::assertFalse($collection->isEmpty());
        self::assertCount(2, $collection->all());
    }

    public function test_with_filter_returns_new_instance(): void
    {
        $collection = QueryFilterCollection::empty();

        $newCollection = $collection->withFilter(
            QueryFilters::equals('Name', 'John')
        );

        self::assertNotSame($collection, $newCollection);
        self::assertTrue($collection->isEmpty());
        self::assertCount(1, $newCollection->all());
    }

    public function test_to_array_flattens_filters(): void
    {
        $collection = QueryFilterCollection::from(
            QueryFilters::equals('Name', 'John'),
            QueryFilters::equals('Age', 30)
        );

        $result = $collection->toArray();

        self::assertCount(2, $result);
    }

    public function test_iterator_returns_filters(): void
    {
        $collection = QueryFilterCollection::from(
            QueryFilters::equals('Name', 'John'),
            QueryFilters::equals('Age', 30)
        );

        $count = 0;

        foreach ($collection as $filter) {
            $count++;
        }

        self::assertSame(2, $count);
    }
}
