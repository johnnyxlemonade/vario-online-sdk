<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Query\Filter;

use Lemonade\Vario\Query\Filter\FilterGroup;
use Lemonade\Vario\Query\Filter\GroupOperator;
use Lemonade\Vario\Query\QueryFilters;
use PHPUnit\Framework\TestCase;

final class FilterGroupTest extends TestCase
{
    public function test_and_group_merges_conditions(): void
    {
        $group = new FilterGroup(GroupOperator::AND);

        $group = $group
            ->withFilter(QueryFilters::equals('Name', 'Test'))
            ->withFilter(QueryFilters::like('Name', 'T%'));

        $result = $group->toArray();

        self::assertCount(1, $result);
        self::assertCount(2, $result[0]);
    }

    public function test_or_group_splits_conditions(): void
    {
        $group = new FilterGroup(GroupOperator::OR);

        $group = $group
            ->withFilter(QueryFilters::equals('Name', 'Test'))
            ->withFilter(QueryFilters::like('Name', 'T%'));

        $result = $group->toArray();

        self::assertCount(2, $result);
        self::assertCount(1, $result[0]);
        self::assertCount(1, $result[1]);
    }

    public function test_empty_group_returns_empty_structure(): void
    {
        $group = new FilterGroup();

        $result = $group->toArray();

        self::assertSame([[]], $result);
    }

}
