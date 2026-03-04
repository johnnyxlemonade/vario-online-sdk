<?php declare(strict_types=1);

namespace Lemonade\Vario\ValueObject;

use Lemonade\Vario\Query\QueryFilterCollection;
use Lemonade\Vario\Query\Filter\QueryFilterInterface;

/**
 * Base immutable query for paged Vario endpoints.
 *
 * @phpstan-consistent-constructor
 */
abstract class AbstractPagedQuery implements PagedQueryInterface
{

    public function __construct(
        protected readonly int $pageIndex = 0,
        protected readonly int $pageLength = 100,
        protected readonly ?string $sortColumn = null,
        protected readonly ?QueryFilterCollection $filters = null,
    ) {}

    /* =========================
     * Interface implementation
     * ========================= */

    public function getPageIndex(): int
    {
        return $this->pageIndex;
    }

    public function getPageLength(): int
    {
        return $this->pageLength;
    }

    public function getSortColumn(): ?string
    {
        return $this->sortColumn;
    }

    public function getFilters(): ?QueryFilterCollection
    {
        return $this->filters;
    }

    protected function newInstance(
        int $pageIndex,
        int $pageLength,
        ?string $sortColumn,
        ?QueryFilterCollection $filters
    ): static {
        return new static($pageIndex, $pageLength, $sortColumn, $filters);
    }

    /* =========================
     * Immutable modifiers
     * ========================= */

    public function nextPage(): static
    {
        return $this->newInstance(
            $this->pageIndex + 1,
            $this->pageLength,
            $this->sortColumn,
            $this->filters
        );
    }

    public function previousPage(): static
    {
        return $this->newInstance(
            max(0, $this->pageIndex - 1),
            $this->pageLength,
            $this->sortColumn,
            $this->filters
        );
    }

    public function nextPageFrom(int $pageCount): ?static
    {
        if ($this->pageIndex + 1 >= $pageCount) {
            return null;
        }

        return $this->nextPage();
    }

    public function withPageIndex(int $pageIndex): static
    {
        return $this->newInstance(
            $pageIndex,
            $this->pageLength,
            $this->sortColumn,
            $this->filters
        );
    }

    public function withPageLength(int $pageLength): static
    {
        return $this->newInstance(
            $this->pageIndex,
            $pageLength,
            $this->sortColumn,
            $this->filters
        );
    }

    public function withSort(string $column): static
    {
        return $this->newInstance(
            $this->pageIndex,
            $this->pageLength,
            $column,
            $this->filters
        );
    }

    public function withFilters(QueryFilterCollection $filters): static
    {
        return $this->newInstance(
            $this->pageIndex,
            $this->pageLength,
            $this->sortColumn,
            $filters
        );
    }

    public function withFilter(QueryFilterInterface $filter): static
    {
        return $this->withFilters(
            ($this->filters ?? QueryFilterCollection::empty())
                ->withFilter($filter)
        );
    }

    /* =========================
     * Shared serialization
     * ========================= */

    /**
     * Shared serialization for paged Vario endpoints.
     *
     * @return array<string,mixed>
     */
    protected function buildPagedArray(string $prefix = ''): array
    {
        $p = $prefix !== '' ? $prefix . '.' : '';

        $data = [
            $p . 'PageIndex'  => $this->pageIndex,
            $p . 'PageLength' => $this->pageLength,
        ];

        if ($this->sortColumn !== null) {
            $data[$p . 'SortColumn'] = $this->sortColumn;
        }

        if ($this->filters !== null && !$this->filters->isEmpty()) {
            $data[$p . 'FilterCriteria'] = $this->filters->toArray();
        }

        return $data;
    }

    public function toArray(): array
    {
        return $this->buildPagedArray();
    }
}
