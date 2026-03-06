<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapping\ProductIdentityMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductIdentity;

/**
 * Class ProductIdentityMapper
 *
 * Maps core product identity fields from DatasetView rows into
 * the ProductIdentity value object.
 *
 * The mapper uses ProductIdentityMapping to determine which
 * DatasetView columns contain the primary identifiers of the product,
 * such as UUID, SKU, catalog number and product name.
 *
 * The UUID represents the stable technical identifier of the product
 * in Vario ERP and is considered required for constructing the
 * ProductIdentity section.
 *
 * If the UUID column is missing or empty, the mapper returns null
 * and the identity section is not included in the Product object.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 *
 * @extends AbstractProductSectionMapper<ProductIdentity>
 */
final class ProductIdentityMapper extends AbstractProductSectionMapper
{
    public function __construct(private readonly ProductIdentityMapping $mapping) {}

    public function map(DatasetRow $row): ?ProductIdentity
    {
        $uuid = $this->mapString($row, $this->mapping->getUuid());

        if ($uuid === null) {
            return null;
        }

        return new ProductIdentity(
            uuid: $uuid,
            sku: $this->mapString($row, $this->mapping->getSku()),
            catalogNumber: $this->mapString($row, $this->mapping->getCatalogNumber()),
            name: $this->mapString($row, $this->mapping->getName()),
        );
    }
}
