<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapping\ProductDescriptionMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductDescription;

/**
 * Class ProductDescriptionMapper
 *
 * Maps product textual description fields from DatasetView rows
 * into the ProductDescription value object.
 *
 * The mapper uses ProductDescriptionMapping to determine which
 * DatasetView columns contain the short description (annotation)
 * and full description text of the product.
 *
 * If both fields are empty or missing, the mapper returns null
 * and the description section is not included in the Product object.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 *
 * @extends AbstractProductSectionMapper<ProductDescription>
 */
final class ProductDescriptionMapper extends AbstractProductSectionMapper
{
    public function __construct(private readonly ProductDescriptionMapping $mapping) {}

    public function map(DatasetRow $row): ?ProductDescription
    {
        $short = $this->mapString($row, $this->mapping->getShortDescription());
        $description = $this->mapString($row, $this->mapping->getDescription());

        if ($short === null && $description === null) {
            return null;
        }

        return new ProductDescription(
            shortDescription: $short,
            description: $description,
        );
    }
}
