<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapping;

/**
 * Class ProductInventoryMapping
 *
 * Defines mapping between DatasetView columns and product inventory fields.
 *
 * Product inventory typically includes stock levels, delivery time,
 * unit of measure, and warranty months. These fields describe the product's
 * availability and logistics information in the ERP system.
 *
 * This mapping allows adapting DatasetView column names used by the ERP
 * system to the SDK's ProductInventory value object without modifying
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
final class ProductInventoryMapping implements ProductSectionMapping
{
    public function __construct(
        private readonly ?string $stock = 'StockTotal',
        private readonly ?string $deliveryTime = 'DeliveryTime',
        private readonly ?string $unit = 'Units',
        private readonly ?string $warrantyMonths = 'Warranty',
    ) {}

    public function getStock(): ?string
    {
        return $this->stock;
    }
    public function getDeliveryTime(): ?string
    {
        return $this->deliveryTime;
    }
    public function getUnit(): ?string
    {
        return $this->unit;
    }
    public function getWarrantyMonths(): ?string
    {
        return $this->warrantyMonths;
    }
}
