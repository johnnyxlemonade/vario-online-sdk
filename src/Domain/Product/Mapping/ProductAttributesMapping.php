<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapping;

/**
 * Class ProductAttributesMapping
 *
 * Defines mapping between product attribute names and DatasetView columns.
 *
 * This mapping allows flexible extraction of dynamic product attributes
 * from Vario DatasetView responses. Each attribute is defined as a pair
 * of attribute name and corresponding dataset column.
 *
 * The mapper uses this configuration to populate the ProductAttributes
 * value object while filtering unsupported or non-scalar values.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class ProductAttributesMapping
{
    /**
     * @param array<string,string> $attributes attributeName => datasetColumn
     */
    public function __construct(
        private readonly array $attributes = []
    ) {}

    /**
     * @return array<string,string>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
