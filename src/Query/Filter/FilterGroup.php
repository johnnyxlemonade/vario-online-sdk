<?php declare(strict_types=1);

namespace Lemonade\Vario\Query\Filter;

/**
 * Composite filter allowing logical grouping.
 */
final class FilterGroup implements QueryFilterInterface
{
    /** @var list<QueryFilterInterface> */
    private array $filters;

    /**
     * @param list<QueryFilterInterface> $filters
     */
    public function __construct(
        private readonly GroupOperator $operator = GroupOperator::AND,
        array $filters = []
    ) {
        $this->filters = $filters;
    }

    public function withFilter(QueryFilterInterface $filter): self
    {
        $clone = clone $this;
        $clone->filters[] = $filter;

        return $clone;
    }

    /**
     * @return list<list<array<string,mixed>>>
     */
    public function toArray(): array
    {
        /** @var list<array<string,mixed>> $conditions */
        $conditions = [];

        foreach ($this->filters as $filter) {
            foreach ($filter->toArray() as $group) {
                foreach ($group as $condition) {
                    $conditions[] = $condition;
                }
            }
        }

        if ($this->operator === GroupOperator::AND) {
            return [$conditions];
        }

        /** @var list<list<array<string,mixed>>> $result */
        $result = [];

        foreach ($conditions as $condition) {
            $result[] = [$condition];
        }

        return $result;
    }

}
