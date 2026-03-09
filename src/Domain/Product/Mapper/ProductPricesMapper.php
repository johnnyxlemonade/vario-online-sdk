<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\Common\VatRate;
use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapping\ProductPricesMapping;
use Lemonade\Vario\Domain\Product\Pricing\Price;
use Lemonade\Vario\Domain\Product\Pricing\PriceCollection;
use Lemonade\Vario\Domain\Product\Pricing\ProductPrices;

/**
 * Class ProductPricesMapper
 *
 * Maps DatasetView rows to ProductPrices domain objects.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 *
 * @extends AbstractProductSectionMapper<ProductPrices>
 */
final class ProductPricesMapper extends AbstractProductSectionMapper
{
    public function __construct(
        private readonly ProductPricesMapping $mapping
    ) {}

    public function map(DatasetRow $row): ?ProductPrices
    {
        $priceColumn = $this->mapping->getBasePrice();
        $vatColumn = $this->mapping->getVatRate();
        $vatIncludedColumn = $this->mapping->getPriceIncludesVat();
        $currencyColumn = $this->mapping->getCurrency();

        if ($priceColumn === null) {
            return null;
        }

        $value = $row->getFloat($priceColumn);

        if ($value === null) {
            return null;
        }

        $vatRate = $vatColumn !== null
            ? VatRate::tryFromNullable($row->getString($vatColumn))
            : null;

        $includesVat = $vatIncludedColumn !== null
            ? $row->getBool($vatIncludedColumn)
            : null;

        $currency = $currencyColumn !== null
            ? Currency::tryFromNullable($row->getString($currencyColumn))
            : null;

        $basePrice = new Price(
            value: $value,
            includesVat: $includesVat ?? false,
            vatRate: $vatRate,
            currency: $currency
        );

        return new ProductPrices(
            levels: new PriceCollection(),
            basePrice: $basePrice
        );
    }
}
