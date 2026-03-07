<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Query;

use Lemonade\Vario\Query\QueryFilters;
use PHPUnit\Framework\TestCase;

final class QueryFiltersTest extends TestCase
{
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
}
