<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\ValueObject;

use Lemonade\Vario\Domain\Product\Pricing\Price;

/*
 * Class ProductPricing
 *
 * Product price information.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class ProductPricing implements ProductSection
{
    public function __construct(
        private readonly ?Price $price,
    ) {}

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function hasPrice(): bool
    {
        return $this->price !== null;
    }

    /**
     * @return array{
     *     price: ?array{
     *         value: float,
     *         includesVat: bool,
     *         vatRate: ?float,
     *         currency: ?string
     *     }
     * }
     */
    public function toArray(): array
    {
        return [
            'price' => $this->price?->toArray(),
        ];
    }
}
