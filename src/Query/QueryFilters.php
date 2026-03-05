<?php

declare(strict_types=1);

namespace Lemonade\Vario\Query;

use Lemonade\Vario\Query\Filter\BetweenFilter;
use Lemonade\Vario\Query\Filter\EndsWithFilter;
use Lemonade\Vario\Query\Filter\EqualsFilter;
use Lemonade\Vario\Query\Filter\FilterGroup;
use Lemonade\Vario\Query\Filter\GreaterOrEqualFilter;
use Lemonade\Vario\Query\Filter\GreaterThanFilter;
use Lemonade\Vario\Query\Filter\GroupOperator;
use Lemonade\Vario\Query\Filter\InFilter;
use Lemonade\Vario\Query\Filter\IsNullFilter;
use Lemonade\Vario\Query\Filter\LessOrEqualFilter;
use Lemonade\Vario\Query\Filter\LessThanFilter;
use Lemonade\Vario\Query\Filter\LikeFilter;
use Lemonade\Vario\Query\Filter\NotEqualsFilter;
use Lemonade\Vario\Query\Filter\NotNullFilter;
use Lemonade\Vario\Query\Filter\QueryFilterInterface;
use Lemonade\Vario\Query\Filter\StartsWithFilter;

/**
 * Class QueryFilters
 *
 * Helper factory for creating query filter instances.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Query
 * @category    Query
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class QueryFilters
{
    public static function equals(string $column, string|int|float|bool $value): EqualsFilter
    {
        return new EqualsFilter($column, $value);
    }

    public static function notEquals(string $column, string|int|float|bool $value): NotEqualsFilter
    {
        return new NotEqualsFilter($column, $value);
    }

    public static function like(string $column, string $value): LikeFilter
    {
        return new LikeFilter($column, $value);
    }

    public static function startsWith(string $column, string $value): StartsWithFilter
    {
        return new StartsWithFilter($column, $value);
    }

    public static function endsWith(string $column, string $value): EndsWithFilter
    {
        return new EndsWithFilter($column, $value);
    }

    public static function between(
        string $column,
        int|float|string $from,
        int|float|string $to
    ): BetweenFilter {
        return new BetweenFilter($column, $from, $to);
    }

    public static function greaterThan(string $column, int|float|string $value): GreaterThanFilter
    {
        return new GreaterThanFilter($column, $value);
    }

    public static function greaterOrEqual(string $column, int|float|string $value): GreaterOrEqualFilter
    {
        return new GreaterOrEqualFilter($column, $value);
    }

    public static function lessThan(string $column, int|float|string $value): LessThanFilter
    {
        return new LessThanFilter($column, $value);
    }

    public static function lessOrEqual(string $column, int|float|string $value): LessOrEqualFilter
    {
        return new LessOrEqualFilter($column, $value);
    }

    /**
     * @param list<string|int|float|bool> $values
     */
    public static function inList(string $column, array $values): InFilter
    {
        return new InFilter($column, $values);
    }

    public static function isNull(string $column): IsNullFilter
    {
        return new IsNullFilter($column);
    }

    public static function notNull(string $column): NotNullFilter
    {
        return new NotNullFilter($column);
    }

    public static function andGroup(QueryFilterInterface ...$filters): FilterGroup
    {
        return new FilterGroup(GroupOperator::AND, array_values($filters));
    }

    public static function orGroup(QueryFilterInterface ...$filters): FilterGroup
    {
        return new FilterGroup(GroupOperator::OR, array_values($filters));
    }
}
