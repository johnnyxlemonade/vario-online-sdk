<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\ValueObject;

/*
 * Class ProductDimensions
 *
 * Physical dimensions of a product.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class ProductDimensions implements ProductSection
{
    private const KG_TO_GRAMS = 1000;

    public function __construct(
        private readonly ?float $width,
        private readonly ?float $height,
        private readonly ?float $depth,
        private readonly ?float $weightGrams,
    ) {}

    public static function fromKg(
        ?float $width,
        ?float $height,
        ?float $depth,
        ?float $weightKg
    ): self {
        return new self(
            width: $width,
            height: $height,
            depth: $depth,
            weightGrams: $weightKg !== null
                ? $weightKg * self::KG_TO_GRAMS
                : null
        );
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function getDepth(): ?float
    {
        return $this->depth;
    }

    public function getWeightGrams(): ?float
    {
        return $this->weightGrams;
    }

    /**
     * @return array{
     *     width: ?float,
     *     height: ?float,
     *     depth: ?float,
     *     weightGrams: ?float
     * }
     */
    public function toArray(): array
    {
        return [
            'width' => $this->width,
            'height' => $this->height,
            'depth' => $this->depth,
            'weightGrams' => $this->weightGrams,
        ];
    }
}
