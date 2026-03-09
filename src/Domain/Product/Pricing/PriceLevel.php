<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Pricing;

/**
 * Class PriceLevel
 *
 * Represents a single price level assigned to a product.
 *
 * Each price level corresponds to a pricing list
 * identified by its code.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class PriceLevel
{
    public function __construct(
        private readonly string $code,
        private readonly Price $price
    ) {}

    public function getCode(): string
    {
        return $this->code;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    /**
     * @return array{
     *     code: string,
     *     price: array{
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
            'code' => $this->code,
            'price' => $this->price->toArray(),
        ];
    }
}
