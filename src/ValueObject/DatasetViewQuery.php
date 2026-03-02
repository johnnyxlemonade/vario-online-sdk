<?php declare(strict_types=1);

namespace Lemonade\Vario\ValueObject;

/**
 * Class DatasetViewQuery
 *
 * Immutable query object pro volání DatasetView API.
 * Zapouzdřuje stránkování, třídění a filtrační kritéria.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario
 * @category    Abstract
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class DatasetViewQuery extends AbstractPagedQuery
{

    /**
     * @param list<array<string,mixed>>|null $filterCriteria
     */
    public function __construct(
        int $pageIndex = 0,
        int $pageLength = 10000,
        ?string $sortColumn = null,
        ?array $filterCriteria = null,
        private readonly DatasetViewInterface $datasetView = DatasetView::KATALOG_ALL,
    ) {
        parent::__construct(
            $pageIndex,
            $pageLength,
            $sortColumn,
            $filterCriteria
        );
    }

    /**
     * Shortcut pro kompletní katalog (Katalog / ALL).
     */
    public static function catalogAll(
        int $pageIndex = 0,
        int $pageLength = 100
    ): self {
        return new self(
            pageIndex: $pageIndex,
            pageLength: $pageLength,
            datasetView: DatasetView::KATALOG_ALL
        );
    }

    /**
     * Obecný factory pro libovolný DatasetView.
     */
    public static function for(
        DatasetViewInterface $view,
        int $pageIndex = 0,
        int $pageLength = 100
    ): self {
        return new self(
            pageIndex: $pageIndex,
            pageLength: $pageLength,
            datasetView: $view
        );
    }

    public function getDatasetView(): DatasetViewInterface
    {
        return $this->datasetView;
    }

    /**
     * Serializace pro DatasetView endpoint.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'Agenda'         => $this->datasetView->agenda(),
            'DatasetViewKey' => $this->datasetView->key(),
            ...$this->buildPagedArray('Pager'),
        ];
    }
}
