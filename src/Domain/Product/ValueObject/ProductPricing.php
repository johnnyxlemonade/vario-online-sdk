<?php declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\ValueObject;

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
final class ProductPricing
{
    public function __construct(
        private readonly ?float $price,
        private readonly ?string $vatRate,
        private readonly ?bool $priceIncludesVat,
    ) {}

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function getVatRate(): ?string
    {
        return $this->vatRate;
    }

    public function isPriceIncludesVat(): ?bool
    {
        return $this->priceIncludesVat;
    }

    /**
     * @return array{
     *     price: ?float,
     *     vatRate: ?string,
     *     priceIncludesVat: ?bool
     * }
     */
    public function toArray(): array
    {
        return [
            'price' => $this->price,
            'vatRate' => $this->vatRate,
            'priceIncludesVat' => $this->priceIncludesVat,
        ];
    }
}
