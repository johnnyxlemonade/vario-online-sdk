<?php declare(strict_types=1);

namespace Lemonade\Vario\ValueObject;

/**
 * Immutable query object pro načítání Incoming Orders.
 */
final class IncomingOrderQuery extends AbstractPagedQuery
{
    public static function firstPage(int $length = 100): self
    {
        return new self(0, $length);
    }

    public function withPage(int $pageIndex): self
    {
        return new self(
            $pageIndex,
            $this->pageLength,
            $this->sortColumn,
            $this->filterCriteria
        );
    }

    public function withPageLength(int $pageLength): self
    {
        return new self(
            $this->pageIndex,
            $pageLength,
            $this->sortColumn,
            $this->filterCriteria
        );
    }

}
