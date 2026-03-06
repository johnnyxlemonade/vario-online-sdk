<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapping\ProductSectionMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductSection;
use RuntimeException;

/**
 * Class AbstractProductSectionMapper
 *
 * Base implementation for product section mappers.
 *
 * This abstract class provides shared utility methods used when
 * converting DatasetView rows into ProductSection value objects.
 * Concrete mapper implementations extend this class and use the
 * helper methods to safely read typed values from DatasetRow.
 *
 * The class itself does not contain mapping configuration. Each
 * concrete mapper receives its own ProductSectionMapping instance
 * describing how DatasetView columns correspond to the domain model.
 *
 * Typical mapping flow:
 *
 * DatasetRow → ProductSectionMapper → ProductSection → Product
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 *
 * @template T of ProductSection
 * @implements ProductSectionMapper<T>
 */
abstract class AbstractProductSectionMapper implements ProductSectionMapper
{
    protected function mapString(DatasetRow $row, ?string $column): ?string
    {
        return $column !== null ? $row->getString($column) : null;
    }

    protected function mapInt(DatasetRow $row, ?string $column): ?int
    {
        return $column !== null ? $row->getInt($column) : null;
    }

    protected function mapFloat(DatasetRow $row, ?string $column): ?float
    {
        return $column !== null ? $row->getFloat($column) : null;
    }

    protected function mapBool(DatasetRow $row, ?string $column): ?bool
    {
        if ($column === null) {
            return null;
        }

        $value = $row->get($column);

        if ($value === null || $value === '') {
            return null;
        }

        return (bool) $value;
    }

    protected function mapBoolFlag(DatasetRow $row, ?string $column): bool
    {
        return (bool) $this->mapBool($row, $column);
    }

    protected function requireString(DatasetRow $row, ?string $column): string
    {
        $value = $this->mapString($row, $column);

        if ($value === null) {
            throw new RuntimeException("Required column '{$column}' missing.");
        }

        return $value;
    }
}
