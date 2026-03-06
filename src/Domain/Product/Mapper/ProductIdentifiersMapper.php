<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapping\ProductIdentifiersMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductIdentifiers;

/**
 * Class ProductIdentifiersMapper
 *
 * Maps external and integration identifiers from DatasetView rows
 * into the ProductIdentifiers value object.
 *
 * The mapper uses ProductIdentifiersMapping to determine which
 * DatasetView columns contain identifiers such as EAN, MPN and
 * supplier-specific product codes.
 *
 * These identifiers complement the primary product identity
 * (UUID, SKU) and are typically used for integrations with
 * marketplaces, suppliers, logistics systems or catalog exports.
 *
 * If none of the configured identifier fields contain a value,
 * the mapper returns null and the identifiers section is omitted
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
 * @extends AbstractProductSectionMapper<ProductIdentifiers>
 */
final class ProductIdentifiersMapper extends AbstractProductSectionMapper
{
    public function __construct(private readonly ProductIdentifiersMapping $mapping) {}

    public function map(DatasetRow $row): ?ProductIdentifiers
    {
        $ean = $this->mapString($row, $this->mapping->getEan());
        $mpn = $this->mapString($row, $this->mapping->getMpn());
        $supplier = $this->mapString($row, $this->mapping->getSupplierCode());

        if ($ean === null && $mpn === null && $supplier === null) {
            return null;
        }

        return new ProductIdentifiers(
            ean: $ean,
            mpn: $mpn,
            supplierCode: $supplier,
        );
    }
}
