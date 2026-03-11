<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapping;

/**
 * Class ProductIdentityMapping
 *
 * Defines mapping between DatasetView columns and core product identity fields.
 *
 * Product identity includes the stable technical identifier (UUID) together
 * with common catalogue identifiers such as SKU, catalog number and product name.
 * These fields represent the primary identification data used when integrating
 * product catalogues with external systems.
 *
 * This mapping allows adapting DatasetView column names used by the ERP
 * system to the SDK's ProductIdentity value object without modifying
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
final class ProductIdentityMapping implements ProductSectionMapping
{
    public function __construct(
        private readonly ?string $uuid = 'ProductRG',
        private readonly ?string $sku = 'ProductID',
        private readonly ?string $catalogNumber = 'CatalogNumber',
        private readonly ?string $name = 'ProductName',
    ) {}

    public function getUuid(): ?string
    {
        return $this->uuid;
    }
    public function getSku(): ?string
    {
        return $this->sku;
    }
    public function getCatalogNumber(): ?string
    {
        return $this->catalogNumber;
    }
    public function getName(): ?string
    {
        return $this->name;
    }
}
