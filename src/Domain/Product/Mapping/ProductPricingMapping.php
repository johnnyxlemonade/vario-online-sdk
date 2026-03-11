<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapping;

/**
 * Class ProductPricingMapping
 *
 * Defines mapping between DatasetView columns and product pricing fields.
 *
 * Product pricing includes the price, VAT rate, and whether the price
 * includes VAT. These fields are essential for e-commerce and ERP integrations,
 * enabling proper pricing and VAT calculations.
 *
 * This mapping allows adapting DatasetView column names used by the ERP
 * system to the SDK's ProductPricing value object without modifying
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
final class ProductPricingMapping implements ProductSectionMapping
{
    public function __construct(
        private readonly ?string $price = 'Price',
        private readonly ?string $vatRate = 'VatRate',
        private readonly ?string $priceIncludesVat = 'PricesIncludeVat',
    ) {}

    public function getPrice(): ?string
    {
        return $this->price;
    }
    public function getVatRate(): ?string
    {
        return $this->vatRate;
    }
    public function getPriceIncludesVat(): ?string
    {
        return $this->priceIncludesVat;
    }
}
