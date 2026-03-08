<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\ValueObject;

use Lemonade\Vario\Query\QueryFilterCollection;
use Lemonade\Vario\Query\QueryFilters;
use Lemonade\Vario\ValueObject\DatasetViewInterface;
use Lemonade\Vario\ValueObject\DatasetViewQuery;
use PHPUnit\Framework\TestCase;

final class DatasetViewQueryTest extends TestCase
{
    private function createDatasetView(): DatasetViewInterface
    {
        return new class implements DatasetViewInterface {
            public function agenda(): string
            {
                return 'TestAgenda';
            }

            public function key(): string
            {
                return 'TestView';
            }
        };
    }

    public function test_previous_page_does_not_go_below_zero(): void
    {
        $view = $this->createDatasetView();

        $query = DatasetViewQuery::for($view);

        $previous = $query->previousPage();

        self::assertSame(0, $previous->getPageIndex());
    }

    public function test_next_page_from_returns_null_when_last_page(): void
    {
        $view = $this->createDatasetView();

        $query = DatasetViewQuery::for($view);

        $next = $query->nextPageFrom(1);

        self::assertNull($next);
    }

    public function test_to_array_without_filters(): void
    {
        $view = $this->createDatasetView();

        $query = DatasetViewQuery::for($view);

        $data = $query->toArray();

        self::assertArrayNotHasKey('Pager.FilterCriteria', $data);
    }

    public function test_factory_for(): void
    {
        $view = $this->createDatasetView();

        $query = DatasetViewQuery::for($view);

        self::assertSame($view, $query->getDatasetView());
        self::assertSame(0, $query->getPageIndex());
        self::assertSame(100, $query->getPageLength());
    }

    public function test_to_array_serialization(): void
    {
        $view = $this->createDatasetView();

        $query = DatasetViewQuery::for($view)
            ->withSort('Id')
            ->withFilter(QueryFilters::equals('Active', true));

        $data = $query->toArray();

        self::assertSame('TestAgenda', $data['Agenda']);
        self::assertSame('TestView', $data['DatasetViewKey']);

        self::assertSame(0, $data['Pager.PageIndex']);
        self::assertSame(100, $data['Pager.PageLength']);
        self::assertSame('Id', $data['Pager.SortColumn']);

        self::assertArrayHasKey('Pager.FilterCriteria', $data);
    }

    public function test_next_page(): void
    {
        $view = $this->createDatasetView();

        $query = DatasetViewQuery::for($view);

        $next = $query->nextPage();

        self::assertSame(1, $next->getPageIndex());
        self::assertSame($view, $next->getDatasetView());
    }

    public function test_with_filters_is_immutable(): void
    {
        $view = $this->createDatasetView();

        $query = DatasetViewQuery::for($view);

        $newQuery = $query->withFilter(
            QueryFilters::equals('Id', 10)
        );

        self::assertNotSame($query, $newQuery);
        self::assertNull($query->getFilters());
        self::assertInstanceOf(QueryFilterCollection::class, $newQuery->getFilters());
    }

    public function test_get_dataset_view_throws_when_not_set(): void
    {
        $query = new DatasetViewQuery();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('DatasetView not set.');

        $query->getDatasetView();
    }
}
