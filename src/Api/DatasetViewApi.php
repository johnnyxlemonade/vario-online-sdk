<?php

declare(strict_types=1);

namespace Lemonade\Vario\Api;

use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Enum\VarioEndpoint;
use Lemonade\Vario\ValueObject\DatasetViewQuery;
use Lemonade\Vario\ValueObject\DatasetViewResult;

/**
 * Class DatasetViewApi
 *
 * API module providing access to DatasetView endpoints of the Vario Online API.
 *
 * DatasetViews are server-defined views used for retrieving structured
 * tabular data (such as product catalogs, customers, orders, etc.).
 * This API module exposes methods for fetching paginated results
 * and for iterating over the entire dataset transparently.
 *
 * Responsibilities of this class include:
 *
 *  - executing DatasetView queries
 *  - handling paginated responses
 *  - providing a generator-based iterator for full dataset traversal
 *
 * The API returns raw transport data (`array<string,mixed>` rows).
 * Domain mapping is intentionally not performed here and should be
 * handled by higher-level mappers.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Api
 * @category    API
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class DatasetViewApi extends AbstractApi
{
    /**
     * Raw DatasetView response.
     *
     * @return array{
     *     Data?: list<array<string,mixed>>,
     *     Pager?: array<string,mixed>
     * }
     */
    public function get(DatasetViewQuery $query): array
    {
        $result = $this->sendQuery(
            HttpMethod::GET,
            VarioEndpoint::DatasetView,
            $query->toArray()
        );

        /** @var array{
         *     Data?: list<array<string,mixed>>,
         *     Pager?: array<string,mixed>
         * } $result */
        return $result;
    }

    /**
     * Typed DatasetView response wrapper.
     */
    public function fetch(DatasetViewQuery $query): DatasetViewResult
    {
        $result = $this->get($query);

        /** @var list<array<string,mixed>> $rows */
        $rows = $result['Data'] ?? [];

        /** @var array<string,mixed> $pager */
        $pager = $result['Pager'] ?? [];

        return new DatasetViewResult($rows, $pager);
    }

    /**
     * Iterates over the entire DatasetView using pagination.
     *
     * @return \Generator<int, array<string,mixed>>
     */
    public function iterate(
        DatasetViewQuery $baseQuery,
        ?int $pageLength = null
    ): \Generator {

        $pageLength ??= $baseQuery->getPageLength();
        $pageIndex = 0;

        do {
            $query = new DatasetViewQuery(
                datasetView: $baseQuery->getDatasetView(),
                pageIndex: $pageIndex,
                pageLength: $pageLength,
                sortColumn: $baseQuery->getSortColumn(),
                filters: $baseQuery->getFilters()
            );

            $result = $this->fetch($query);

            foreach ($result->getRows() as $row) {
                yield $row;
            }

            $pageIndex++;
            $pageCount = $result->getPageCount();

        } while ($pageIndex < $pageCount);
    }
}
