<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Query;

use Lemonade\Vario\Query\Filter\BetweenFilter;
use Lemonade\Vario\Query\Filter\EndsWithFilter;
use Lemonade\Vario\Query\Filter\EqualsFilter;
use Lemonade\Vario\Query\Filter\GreaterOrEqualFilter;
use Lemonade\Vario\Query\Filter\GreaterThanFilter;
use Lemonade\Vario\Query\Filter\InFilter;
use Lemonade\Vario\Query\Filter\IsNullFilter;
use Lemonade\Vario\Query\Filter\LessOrEqualFilter;
use Lemonade\Vario\Query\Filter\LessThanFilter;
use Lemonade\Vario\Query\Filter\LikeFilter;
use Lemonade\Vario\Query\Filter\NotEqualsFilter;
use Lemonade\Vario\Query\Filter\NotNullFilter;
use Lemonade\Vario\Query\Filter\StartsWithFilter;
use Lemonade\Vario\Query\QueryFilters;
use PHPUnit\Framework\TestCase;

final class QueryFiltersTest extends TestCase
{
    public function test_equals_factory(): void
    {
        $filter = QueryFilters::equals('Name', 'John');

        self::assertInstanceOf(EqualsFilter::class, $filter);
    }

    public function test_not_equals_factory(): void
    {
        $filter = QueryFilters::notEquals('Name', 'John');

        self::assertInstanceOf(NotEqualsFilter::class, $filter);
    }

    public function test_like_factory(): void
    {
        $filter = QueryFilters::like('Name', 'Jo%');

        self::assertInstanceOf(LikeFilter::class, $filter);
    }

    public function test_starts_with_factory(): void
    {
        $filter = QueryFilters::startsWith('Name', 'Jo');

        self::assertInstanceOf(StartsWithFilter::class, $filter);
    }

    public function test_ends_with_factory(): void
    {
        $filter = QueryFilters::endsWith('Name', 'son');

        self::assertInstanceOf(EndsWithFilter::class, $filter);
    }

    public function test_between_factory(): void
    {
        $filter = QueryFilters::between('Price', 10, 20);

        self::assertInstanceOf(BetweenFilter::class, $filter);
    }

    public function test_greater_than_factory(): void
    {
        $filter = QueryFilters::greaterThan('Price', 10);

        self::assertInstanceOf(GreaterThanFilter::class, $filter);
    }

    public function test_greater_or_equal_factory(): void
    {
        $filter = QueryFilters::greaterOrEqual('Price', 10);

        self::assertInstanceOf(GreaterOrEqualFilter::class, $filter);
    }

    public function test_less_than_factory(): void
    {
        $filter = QueryFilters::lessThan('Price', 10);

        self::assertInstanceOf(LessThanFilter::class, $filter);
    }

    public function test_less_or_equal_factory(): void
    {
        $filter = QueryFilters::lessOrEqual('Price', 10);

        self::assertInstanceOf(LessOrEqualFilter::class, $filter);
    }

    public function test_in_list_factory(): void
    {
        $filter = QueryFilters::inList('Id', [1,2,3]);

        self::assertInstanceOf(InFilter::class, $filter);
    }

    public function test_is_null_factory(): void
    {
        $filter = QueryFilters::isNull('DeletedAt');

        self::assertInstanceOf(IsNullFilter::class, $filter);
    }

    public function test_not_null_factory(): void
    {
        $filter = QueryFilters::notNull('DeletedAt');

        self::assertInstanceOf(NotNullFilter::class, $filter);
    }

    public function test_or_group(): void
    {
        $group = QueryFilters::orGroup(
            QueryFilters::equals('Name', 'Test'),
            QueryFilters::like('Name', 'T%')
        );

        $array = $group->toArray();

        self::assertCount(2, $array);
        self::assertCount(1, $array[0]);
    }

    public function test_and_group(): void
    {
        $group = QueryFilters::andGroup(
            QueryFilters::equals('Name', 'Test'),
            QueryFilters::like('Name', 'T%')
        );

        $array = $group->toArray();

        self::assertCount(1, $array);
        self::assertCount(2, $array[0]);
    }
}
