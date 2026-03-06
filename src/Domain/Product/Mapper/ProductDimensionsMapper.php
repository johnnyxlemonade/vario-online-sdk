<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapping\ProductDimensionsMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductDimensions;

/**
 * Class ProductDimensionsMapper
 *
 * Maps product physical dimension fields from DatasetView rows
 * into the ProductDimensions value object.
 *
 * The mapper uses ProductDimensionsMapping to determine which
 * DatasetView columns represent width, height, depth and weight.
 * Values are read as floats and normalized by the value object
 * factory (e.g. conversion from kilograms).
 *
 * If all dimension fields are missing or empty, the mapper returns
 * null and the dimensions section is omitted from the Product object.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 *
 * @extends AbstractProductSectionMapper<ProductDimensions>
 */
final class ProductDimensionsMapper extends AbstractProductSectionMapper
{
    public function __construct(private readonly ProductDimensionsMapping $mapping) {}

    public function map(DatasetRow $row): ?ProductDimensions
    {
        $width = $this->mapFloat($row, $this->mapping->getWidth());
        $height = $this->mapFloat($row, $this->mapping->getHeight());
        $depth = $this->mapFloat($row, $this->mapping->getDepth());
        $weight = $this->mapFloat($row, $this->mapping->getWeightKg());

        if ($width === null && $height === null && $depth === null && $weight === null) {
            return null;
        }

        return ProductDimensions::fromKg(
            width: $width,
            height: $height,
            depth: $depth,
            weightKg: $weight,
        );
    }
}
