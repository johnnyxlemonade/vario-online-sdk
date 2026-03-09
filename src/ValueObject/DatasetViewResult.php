<?php

declare(strict_types=1);

namespace Lemonade\Vario\ValueObject;

/**
 * Class DatasetViewResult
 *
 * Immutable transport container representing a DatasetView response
 * returned by the Vario Online API.
 *
 * It encapsulates the tabular dataset rows together with pager metadata
 * describing pagination state.
 *
 * This object does not perform any domain mapping. It only represents
 * the raw transport structure returned by the API.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\ValueObject
 * @category    Transport
 */
final class DatasetViewResult
{
    /** @var list<array<string,mixed>> */
    private array $rows;

    /** @var array<string,mixed> */
    private array $pager;

    /**
     * @param list<array<string,mixed>> $rows
     * @param array<string,mixed> $pager
     */
    public function __construct(array $rows, array $pager = [])
    {
        $this->rows = $rows;
        $this->pager = $pager;
    }

    /**
     * @return list<array<string,mixed>>
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * @return array<string,mixed>
     */
    public function getPager(): array
    {
        return $this->pager;
    }

    public function hasRows(): bool
    {
        return $this->rows !== [];
    }

    public function getPageCount(): int
    {
        $value = $this->pager['PageCount'] ?? 0;

        /** @var int|string|float $value */
        return (int) $value;
    }

    public function getPageIndex(): int
    {
        $value = $this->pager['PageIndex'] ?? 0;

        /** @var int|string|float $value */
        return (int) $value;
    }

    public function getPageLength(): int
    {
        $value = $this->pager['PageLength'] ?? 0;

        /** @var int|string|float $value */
        return (int) $value;
    }

    public function getRecordCount(): int
    {
        $value = $this->pager['RecordCount'] ?? 0;

        /** @var int|string|float $value */
        return (int) $value;
    }

    /**
     * @return array{
     *     Data:list<array<string,mixed>>,
     *     Pager:array<string,mixed>
     * }
     */
    public function toArray(): array
    {
        return [
            'Data' => $this->rows,
            'Pager' => $this->pager,
        ];
    }
}
