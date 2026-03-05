<?php

declare(strict_types=1);

namespace Lemonade\Vario\Api;

use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Enum\VarioEndpoint;
use Lemonade\Vario\ValueObject\DatasetViewQuery;

/**
 * Class DatasetViewApi
 *
 * API modul pro práci s DatasetView ve Vario Online.
 * Umožňuje načítání dat pomocí stránkování a iteraci
 * nad kompletním DatasetView.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Api
 * @category    Api
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class DatasetViewApi extends AbstractApi
{
    /**
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
     * Iteruje nad celým DatasetView pomocí stránkování.
     *
     * @return \Generator<int, array<string,mixed>>
     */
    public function iterate(
        DatasetViewQuery $baseQuery,
        int $pageLength = 100
    ): \Generator {
        $pageIndex = 0;

        do {
            $query = new DatasetViewQuery(
                datasetView: $baseQuery->getDatasetView(),
                pageIndex: $pageIndex,
                pageLength: $pageLength,
                sortColumn: $baseQuery->getSortColumn(),
                filters: $baseQuery->getFilters()
            );

            $result = $this->get($query);

            /** @var list<array<string,mixed>> $rows */
            $rows = $result['Data'] ?? [];

            /** @var array<string,mixed> $pager */
            $pager = $result['Pager'] ?? [];

            foreach ($rows as $row) {
                yield $row;
            }

            $pageIndex++;

            // PHPStan level 9 safe narrowing
            $pageCountValue = $pager['PageCount'] ?? 0;
            /** @var int|string|float $pageCountValue */
            $pageCount = (int) $pageCountValue;

        } while ($pageIndex < $pageCount);
    }
}
