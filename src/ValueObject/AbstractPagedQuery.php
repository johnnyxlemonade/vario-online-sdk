<?php declare(strict_types=1);

namespace Lemonade\Vario\ValueObject;

/**
 * Base immutable query for paged Vario endpoints.
 *
 * @phpstan-consistent-constructor
 */
abstract class AbstractPagedQuery implements PagedQueryInterface
{
    /**
     * @param list<array<string,mixed>>|null $filterCriteria
     */
    public function __construct(
        protected readonly int $pageIndex = 0,
        protected readonly int $pageLength = 100,
        protected readonly ?string $sortColumn = null,
        protected readonly ?array $filterCriteria = null,
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

    /**
     * @return list<array<string,mixed>>|null
     */
    public function getFilterCriteria(): ?array
    {
        return $this->filterCriteria;
    }

    /* =========================
     * Immutable modifiers
     * ========================= */

    public function withSort(string $column): static
    {
        return new static(
            pageIndex: $this->pageIndex,
            pageLength: $this->pageLength,
            sortColumn: $column,
            filterCriteria: $this->filterCriteria
        );
    }

    /**
     * @param list<array<string,mixed>> $criteria
     */
    public function withCriteria(array $criteria): static
    {
        return new static(
            pageIndex: $this->pageIndex,
            pageLength: $this->pageLength,
            sortColumn: $this->sortColumn,
            filterCriteria: $criteria
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

        if ($this->filterCriteria !== null && $this->filterCriteria !== []) {
            $data[$p . 'FilterCriteria'] = $this->filterCriteria;
        }

        return $data;
    }

    public function toArray(): array
    {
        return $this->buildPagedArray();
    }
}
