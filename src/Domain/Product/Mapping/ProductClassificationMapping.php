<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapping;

/**
 * Class ProductClassificationMapping
 *
 * Defines mapping between DatasetView columns and product classification fields.
 *
 * Product classification typically includes category identifiers, category names
 * and brand information. This mapping allows adapting different DatasetView
 * structures to the SDK's ProductClassification value object without changing
 * the domain model.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class ProductClassificationMapping
{
    public function __construct(
        private readonly ?string $categoryId = 'KategorieId',
        private readonly ?string $categoryName = 'Kategorie',
        private readonly ?string $brand = 'Znacka',
    ) {}

    public function getCategoryId(): ?string
    {
        return $this->categoryId;
    }

    public function getCategoryName(): ?string
    {
        return $this->categoryName;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }
}
