<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapping\ProductPricingMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductPricing;

/**
 * Class ProductPricingMapper
 *
 * Maps product pricing data from DatasetView rows into
 * the ProductPricing value object.
 *
 * The mapper uses ProductPricingMapping to determine which
 * DatasetView columns contain pricing information such as
 * base price, VAT rate and whether the price includes VAT.
 *
 * These fields represent the primary pricing data exported
 * from Vario ERP and are commonly used in catalogue feeds,
 * e-commerce integrations and pricing synchronisation.
 *
 * If none of the configured pricing fields contain a value,
 * the mapper returns null and the pricing section is omitted
 * from the Product object.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 *
 * @extends AbstractProductSectionMapper<ProductPricing>
 */
final class ProductPricingMapper extends AbstractProductSectionMapper
{
    public function __construct(
        private readonly ProductPricingMapping $mapping
    ) {}

    public function map(DatasetRow $row): ?ProductPricing
    {
        $price = $this->mapFloat($row, $this->mapping->getPrice());
        $vatRate = $this->mapString($row, $this->mapping->getVatRate());
        $includesVat = $this->mapBool($row, $this->mapping->getPriceIncludesVat());

        if ($price === null && $vatRate === null && $includesVat === null) {
            return null;
        }

        return new ProductPricing(
            price: $price,
            vatRate: $vatRate,
            priceIncludesVat: $includesVat,
        );
    }
}
