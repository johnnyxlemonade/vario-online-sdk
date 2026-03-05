<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\ValueObject;

/**
 * Class ProductClassification
 *
 * Represents the catalogue classification of a product.
 *
 * Contains information used to organize products within
 * catalog structures such as categories and brands.
 *
 * This data is commonly used for product navigation,
 * filtering, and marketplace integrations.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class ProductClassification
{
    public function __construct(
        private readonly ?string $categoryId,
        private readonly ?string $categoryName,
        private readonly ?string $brand,
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

    /**
     * @return array{
     *     categoryId: ?string,
     *     categoryName: ?string,
     *     brand: ?string
     * }
     */
    public function toArray(): array
    {
        return [
            'categoryId' => $this->categoryId,
            'categoryName' => $this->categoryName,
            'brand' => $this->brand,
        ];
    }
}
