<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapping;

/**
 * Class ProductDimensionsMapping
 *
 * Defines mapping between DatasetView columns and product dimension fields.
 *
 * Product dimensions typically include width, height, depth and weight.
 * The weight value is expected to be provided in kilograms and will be
 * converted to grams by the ProductDimensions value object.
 *
 * This mapping allows adapting DatasetView column names to the SDK's
 * ProductDimensions value object without modifying the domain model.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class ProductDimensionsMapping
{
    public function __construct(
        private readonly ?string $width = 'Sirka',
        private readonly ?string $height = 'Vyska',
        private readonly ?string $depth = 'Hloubka',
        private readonly ?string $weightKg = 'Hmotnost_kg',
    ) {}

    public function getWidth(): ?string
    {
        return $this->width;
    }
    public function getHeight(): ?string
    {
        return $this->height;
    }
    public function getDepth(): ?string
    {
        return $this->depth;
    }
    public function getWeightKg(): ?string
    {
        return $this->weightKg;
    }
}
