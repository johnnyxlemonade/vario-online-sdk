<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\ValueObject;

/*
 * Class ProductInventory
 *
 * Inventory and logistics information.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class ProductInventory implements ProductSection
{
    public function __construct(
        private readonly ?int $stock,
        private readonly ?int $deliveryTime,
        private readonly ?string $unit,
        private readonly ?int $warrantyMonths,
        private readonly ?string $packaging
    ) {}

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function getDeliveryTime(): ?int
    {
        return $this->deliveryTime;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function getWarrantyMonths(): ?int
    {
        return $this->warrantyMonths;
    }

    public function getPackaging(): ?string
    {
        return $this->packaging;
    }

    /**
     * @return array{
     *     stock: ?int,
     *     deliveryTime: ?int,
     *     unit: ?string,
     *     warrantyMonths: ?int,
     *     packaging: ?string,
     * }
     */
    public function toArray(): array
    {
        return [
            'stock' => $this->stock,
            'deliveryTime' => $this->deliveryTime,
            'unit' => $this->unit,
            'warrantyMonths' => $this->warrantyMonths,
            'packaging' => $this->packaging,
        ];
    }
}
