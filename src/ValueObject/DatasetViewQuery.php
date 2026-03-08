<?php

declare(strict_types=1);

namespace Lemonade\Vario\ValueObject;

use Lemonade\Vario\Query\QueryFilterCollection;

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
    public function __construct(
        int $pageIndex = 0,
        int $pageLength = 100,
        ?string $sortColumn = null,
        ?QueryFilterCollection $filters = null,
        private readonly ?DatasetViewInterface $datasetView = null,
    ) {
        parent::__construct(
            $pageIndex,
            $pageLength,
            $sortColumn,
            $filters
        );
    }

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
        if ($this->datasetView === null) {
            throw new \LogicException('DatasetView not set.');
        }

        return $this->datasetView;
    }

    protected function newInstance(
        int $pageIndex,
        int $pageLength,
        ?string $sortColumn,
        ?QueryFilterCollection $filters
    ): static {
        return new static(
            pageIndex: $pageIndex,
            pageLength: $pageLength,
            sortColumn: $sortColumn,
            filters: $filters,
            datasetView: $this->datasetView
        );
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        $view = $this->getDatasetView();

        return [
            'Agenda' => $view->agenda(),
            'DatasetViewKey' => $view->key(),
            ...$this->buildPagedArray('Pager'),
        ];
    }
}
