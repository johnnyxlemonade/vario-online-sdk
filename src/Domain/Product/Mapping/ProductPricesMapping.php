<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapping;

/**
 * Class ProductPricesMapping
 *
 * Defines DatasetView column mapping for product pricing fields.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class ProductPricesMapping implements ProductSectionMapping
{
    public function __construct(
        private readonly ?string $basePrice = 'Cena_Cenik',
        private readonly ?string $vatRate = 'Sazba_DPH',
        private readonly ?string $priceIncludesVat = 'Ceny_vcetne_DPH',
        private readonly ?string $currency = null
    ) {}

    public function getBasePrice(): ?string
    {
        return $this->basePrice;
    }

    public function getVatRate(): ?string
    {
        return $this->vatRate;
    }

    public function getPriceIncludesVat(): ?string
    {
        return $this->priceIncludesVat;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }
}
