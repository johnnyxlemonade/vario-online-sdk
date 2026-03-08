<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\ValueObject;

use Lemonade\Vario\Query\QueryFilterCollection;
use Lemonade\Vario\Query\QueryFilters;
use Lemonade\Vario\ValueObject\AbstractPagedQuery;
use PHPUnit\Framework\TestCase;

final class AbstractPagedQueryTest extends TestCase
{
    private function createQuery(): AbstractPagedQuery
    {
        return new class extends AbstractPagedQuery {};
    }

    public function test_with_page_index(): void
    {
        $query = $this->createQuery();

        $new = $query->withPageIndex(5);

        self::assertSame(5, $new->getPageIndex());
        self::assertNotSame($query, $new);
    }

    public function test_with_page_length(): void
    {
        $query = $this->createQuery();

        $new = $query->withPageLength(500);

        self::assertSame(500, $new->getPageLength());
    }

    public function test_with_sort(): void
    {
        $query = $this->createQuery();

        $new = $query->withSort('Id');

        self::assertSame('Id', $new->getSortColumn());
    }

    public function test_next_page(): void
    {
        $query = $this->createQuery();

        $next = $query->nextPage();

        self::assertSame(1, $next->getPageIndex());
    }

    public function test_previous_page(): void
    {
        $query = $this->createQuery()->withPageIndex(2);

        $previous = $query->previousPage();

        self::assertSame(1, $previous->getPageIndex());
    }

    public function test_with_filter(): void
    {
        $query = $this->createQuery();

        $new = $query->withFilter(
            QueryFilters::equals('Id', 10)
        );

        self::assertInstanceOf(QueryFilterCollection::class, $new->getFilters());
    }

    public function test_next_page_from(): void
    {
        $query = $this->createQuery();

        self::assertNull($query->nextPageFrom(1));
    }

    public function test_next_page_from_returns_next_page(): void
    {
        $query = $this->createQuery(); // pageIndex = 0

        $next = $query->nextPageFrom(10);

        self::assertNotNull($next);
        self::assertSame(1, $next->getPageIndex());
    }

    public function test_build_paged_array(): void
    {
        $query = $this->createQuery()
            ->withPageIndex(3)
            ->withPageLength(50)
            ->withSort('Id');

        $data = $query->toArray();

        self::assertSame(3, $data['PageIndex']);
        self::assertSame(50, $data['PageLength']);
        self::assertSame('Id', $data['SortColumn']);
    }
}
