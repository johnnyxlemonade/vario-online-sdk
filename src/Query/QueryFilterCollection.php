<?php

declare(strict_types=1);

/**
 * Class QueryFilterCollection
 *
 * Immutable collection of query filters.
 * Used to aggregate multiple filters before
 * serialization to Vario API filter criteria.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Query
 * @category    Query
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */

namespace Lemonade\Vario\Query;

use IteratorAggregate;
use Lemonade\Vario\Query\Filter\QueryFilterInterface;
use Traversable;

/**
 * @implements IteratorAggregate<int,QueryFilterInterface>
 */
final class QueryFilterCollection implements IteratorAggregate
{
    /** @var list<QueryFilterInterface> */
    private array $filters;

    /**
     * @param array<int, QueryFilterInterface> $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = array_values($filters);
    }

    public static function empty(): self
    {
        return new self();
    }

    public static function from(QueryFilterInterface ...$filters): self
    {
        return new self(array_values($filters));
    }

    public function withFilter(QueryFilterInterface $filter): self
    {
        $clone = clone $this;
        $clone->filters[] = $filter;

        return $clone;
    }

    /**
     * @return list<QueryFilterInterface>
     */
    public function all(): array
    {
        return $this->filters;
    }

    public function isEmpty(): bool
    {
        return $this->filters === [];
    }

    /**
     * @return list<list<array<string,mixed>>>
     */
    public function toArray(): array
    {
        /** @var list<list<array<string,mixed>>> $result */
        $result = [];

        foreach ($this->filters as $filter) {
            foreach ($filter->toArray() as $group) {
                /** @var list<array<string,mixed>> $group */
                $result[] = $group;
            }
        }

        return $result;
    }
    public function getIterator(): Traversable
    {
        yield from $this->filters;
    }
}
