<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapping\ProductInventoryMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductInventory;

/**
 * Class ProductInventoryMapper
 *
 * Maps product inventory and logistics data from DatasetView rows
 * into the ProductInventory value object.
 *
 * The mapper uses ProductInventoryMapping to determine which
 * DatasetView columns represent stock quantity, delivery time,
 * unit of measure and warranty period.
 *
 * These fields describe the operational availability of the product
 * and are typically used for e-commerce integrations, warehouse
 * synchronization and availability calculations.
 *
 * If none of the configured inventory fields contain a value,
 * the mapper returns null and the inventory section is omitted
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
 * @extends AbstractProductSectionMapper<ProductInventory>
 */
final class ProductInventoryMapper extends AbstractProductSectionMapper
{
    public function __construct(
        private readonly ProductInventoryMapping $mapping
    ) {}

    public function map(DatasetRow $row): ?ProductInventory
    {
        $stock = $this->mapInt($row, $this->mapping->getStock());
        $deliveryTime = $this->mapInt($row, $this->mapping->getDeliveryTime());
        $unit = $this->mapString($row, $this->mapping->getUnit());
        $warrantyMonths = $this->mapInt($row, $this->mapping->getWarrantyMonths());

        if ($stock === null && $deliveryTime === null && $unit === null && $warrantyMonths === null) {
            return null;
        }

        return new ProductInventory(
            stock: $stock,
            deliveryTime: $deliveryTime,
            unit: $unit,
            warrantyMonths: $warrantyMonths,
        );
    }
}
