<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Pricing;

use Lemonade\Vario\Domain\Product\ValueObject\ProductSection;

/**
 * Class ProductPrices
 *
 * Represents a structured set of prices assigned to a product.
 *
 * Provides access to the base product price and additional
 * price levels defined in pricing lists.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class ProductPrices implements ProductSection
{
    public function __construct(
        private readonly PriceCollection $levels,
        private readonly ?Price $basePrice = null
    ) {}

    public function getBasePrice(): ?Price
    {
        return $this->basePrice;
    }

    public function getLevels(): PriceCollection
    {
        return $this->levels;
    }

    public function hasBasePrice(): bool
    {
        return $this->basePrice !== null;
    }

    /**
     * @return array{
     *     basePrice: ?array{
     *         value: float,
     *         includesVat: bool,
     *         vatRate: ?float,
     *         currency: ?string
     *     },
     *     levels: array<string,array{
     *         code: string,
     *         price: array{
     *             value: float,
     *             includesVat: bool,
     *             vatRate: ?float,
     *             currency: ?string
     *         }
     *     }>
     * }
     */
    public function toArray(): array
    {
        return [
            'basePrice' => $this->basePrice?->toArray(),
            'levels' => $this->levels->toArray(),
        ];
    }
}
