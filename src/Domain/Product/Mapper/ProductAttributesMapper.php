<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapping\ProductAttributesMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductAttributes;

/**
 * Class ProductAttributesMapper
 *
 * Maps dynamic product attributes from DatasetView rows into
 * the ProductAttributes value object.
 *
 * The mapper reads attribute definitions from ProductAttributesMapping,
 * where each attribute is defined as a pair of attribute name and
 * DatasetView column. Only scalar values are accepted; unsupported
 * or complex values are ignored.
 *
 * If no attributes are resolved from the dataset row, the mapper
 * returns null and the section is omitted from the Product object.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 *
 * @extends AbstractProductSectionMapper<ProductAttributes>
 */
final class ProductAttributesMapper extends AbstractProductSectionMapper
{
    public function __construct(
        private readonly ProductAttributesMapping $mapping
    ) {}

    public function map(DatasetRow $row): ?ProductAttributes
    {
        $attributes = [];

        foreach ($this->mapping->getAttributes() as $name => $column) {
            $value = $row->get($column);

            if (is_scalar($value) || $value === null) {
                $attributes[$name] = $value;
            }
        }

        if ($attributes === []) {
            return null;
        }

        return new ProductAttributes($attributes);
    }
}
