<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapping\ProductClassificationMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductClassification;

/**
 * Class ProductClassificationMapper
 *
 * Maps product classification data from DatasetView rows into
 * the ProductClassification value object.
 *
 * The mapper uses ProductClassificationMapping to determine which
 * DatasetView columns represent classification fields such as
 * category identifier, category name and brand.
 *
 * If none of the configured fields are present in the dataset row,
 * the mapper returns null and the classification section is omitted
 * from the Product object.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 *
 * @extends AbstractProductSectionMapper<ProductClassification>
 */
final class ProductClassificationMapper extends AbstractProductSectionMapper
{
    public function __construct(
        private readonly ProductClassificationMapping $mapping
    ) {}

    public function map(DatasetRow $row): ?ProductClassification
    {
        $categoryId = $this->mapString($row, $this->mapping->getCategoryId());
        $categoryName = $this->mapString($row, $this->mapping->getCategoryName());
        $brand = $this->mapString($row, $this->mapping->getBrand());

        if ($categoryId === null && $categoryName === null && $brand === null) {
            return null;
        }

        return new ProductClassification(
            categoryId: $categoryId,
            categoryName: $categoryName,
            brand: $brand,
        );
    }
}
