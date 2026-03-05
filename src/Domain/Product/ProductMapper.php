<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product;

use Lemonade\Vario\Domain\Product\Mapping\ProductAttributesMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductClassificationMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductDescriptionMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductDimensionsMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductFlagsMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductIdentifiersMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductIdentityMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductInventoryMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductPricingMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductAttributes;
use Lemonade\Vario\Domain\Product\ValueObject\ProductClassification;
use Lemonade\Vario\Domain\Product\ValueObject\ProductDescription;
use Lemonade\Vario\Domain\Product\ValueObject\ProductDimensions;
use Lemonade\Vario\Domain\Product\ValueObject\ProductFlag;
use Lemonade\Vario\Domain\Product\ValueObject\ProductFlags;
use Lemonade\Vario\Domain\Product\ValueObject\ProductIdentifiers;
use Lemonade\Vario\Domain\Product\ValueObject\ProductIdentity;
use Lemonade\Vario\Domain\Product\ValueObject\ProductInventory;
use Lemonade\Vario\Domain\Product\ValueObject\ProductPricing;

/**
 * Class ProductMapper
 *
 * Maps DatasetView rows into Product domain objects.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class ProductMapper
{
    public function __construct(
        private ProductDatasetMapping $mapping
    ) {}

    public function map(DatasetRow $row): Product
    {
        return new Product(
            identity: $this->mapIdentity($row, $this->mapping->identity()),
            description: $this->mapDescription($row, $this->mapping->description()),
            flags: $this->mapFlags($row, $this->mapping->flags()),
            dimensions: $this->mapDimensions($row, $this->mapping->dimensions()),
            pricing: $this->mapPricing($row, $this->mapping->pricing()),
            inventory: $this->mapInventory($row, $this->mapping->inventory()),
            identifiers: $this->mapIdentifiers($row, $this->mapping->identifiers()),
            classification: $this->mapClassification($row, $this->mapping->classification()),
            attributes: $this->mapAttributes($row, $this->mapping->attributes()),
        );
    }

    private function mapIdentity(DatasetRow $row, ProductIdentityMapping $m): ProductIdentity
    {
        return new ProductIdentity(
            uuid: $this->requireString($row, $m->getUuid()),
            sku: $this->mapString($row, $m->getSku()),
            catalogNumber: $this->mapString($row, $m->getCatalogNumber()),
            name: $this->mapString($row, $m->getName()),
        );
    }

    private function mapDescription(DatasetRow $row, ProductDescriptionMapping $m): ProductDescription
    {
        return new ProductDescription(
            shortDescription: $this->mapString($row, $m->getShortDescription()),
            description: $this->mapString($row, $m->getDescription()),
        );
    }

    private function mapFlags(DatasetRow $row, ProductFlagsMapping $m): ProductFlags
    {
        $flags = new ProductFlags();

        $flags->set(ProductFlag::SALE, $this->mapBoolFlag($row, $m->isSale()));
        $flags->set(ProductFlag::NEW, $this->mapBoolFlag($row, $m->isNew()));
        $flags->set(ProductFlag::DISCOUNT, $this->mapBoolFlag($row, $m->isDiscount()));
        $flags->set(ProductFlag::CLEARANCE, $this->mapBoolFlag($row, $m->isClearance()));
        $flags->set(ProductFlag::RECOMMENDED, $this->mapBoolFlag($row, $m->isRecommended()));
        $flags->set(ProductFlag::PREPARING, $this->mapBoolFlag($row, $m->isPreparing()));

        return $flags;
    }

    private function mapDimensions(DatasetRow $row, ProductDimensionsMapping $m): ProductDimensions
    {
        return ProductDimensions::fromKg(
            width: $this->mapFloat($row, $m->getWidth()),
            height: $this->mapFloat($row, $m->getHeight()),
            depth: $this->mapFloat($row, $m->getDepth()),
            weightKg: $this->mapFloat($row, $m->getWeightKg()),
        );
    }

    private function mapPricing(DatasetRow $row, ProductPricingMapping $m): ProductPricing
    {
        return new ProductPricing(
            price: $this->mapFloat($row, $m->getPrice()),
            vatRate: $this->mapString($row, $m->getVatRate()),
            priceIncludesVat: $this->mapBool($row, $m->getPriceIncludesVat()),
        );
    }

    private function mapInventory(DatasetRow $row, ProductInventoryMapping $m): ProductInventory
    {
        return new ProductInventory(
            stock: $this->mapInt($row, $m->getStock()),
            deliveryTime: $this->mapInt($row, $m->getDeliveryTime()),
            unit: $this->mapString($row, $m->getUnit()),
            warrantyMonths: $this->mapInt($row, $m->getWarrantyMonths()),
        );
    }

    private function mapIdentifiers(DatasetRow $row, ProductIdentifiersMapping $m): ProductIdentifiers
    {
        return new ProductIdentifiers(
            ean: $this->mapString($row, $m->getEan()),
            mpn: $this->mapString($row, $m->getMpn()),
            supplierCode: $this->mapString($row, $m->getSupplierCode()),
        );
    }

    private function mapClassification(DatasetRow $row, ProductClassificationMapping $m): ProductClassification
    {
        return new ProductClassification(
            categoryId: $this->mapString($row, $m->getCategoryId()),
            categoryName: $this->mapString($row, $m->getCategoryName()),
            brand: $this->mapString($row, $m->getBrand()),
        );
    }

    private function mapAttributes(DatasetRow $row, ProductAttributesMapping $m): ProductAttributes
    {
        $attributes = [];

        foreach ($m->getAttributes() as $key => $column) {
            $value = $row->get($column);

            if (is_scalar($value) || $value === null) {
                $attributes[$key] = $value;
            }
        }

        return new ProductAttributes($attributes);
    }

    private function mapString(DatasetRow $row, ?string $column): ?string
    {
        return $column !== null ? $row->getString($column) : null;
    }

    private function mapFloat(DatasetRow $row, ?string $column): ?float
    {
        return $column !== null ? $row->getFloat($column) : null;
    }

    private function mapInt(DatasetRow $row, ?string $column): ?int
    {
        return $column !== null ? $row->getInt($column) : null;
    }

    private function mapBool(DatasetRow $row, ?string $column): ?bool
    {
        if ($column === null) {
            return null;
        }

        $value = $row->get($column);

        if ($value === null || $value === '') {
            return null;
        }

        return (bool) $value;
    }

    private function mapBoolFlag(DatasetRow $row, ?string $column): bool
    {
        return (bool) $this->mapBool($row, $column);
    }

    private function requireString(DatasetRow $row, ?string $column): string
    {
        $value = $this->mapString($row, $column);

        if ($value === null) {
            throw new \RuntimeException("Required column '{$column}' missing in dataset row.");
        }

        return $value;
    }
}
