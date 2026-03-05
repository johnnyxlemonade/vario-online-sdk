<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapping;

/**
 * Class ProductIdentifiersMapping
 *
 * Defines mapping between DatasetView columns and product identifier fields.
 *
 * Product identifiers typically include standardized product codes such as
 * EAN (European Article Number), MPN (Manufacturer Part Number) and
 * supplier-specific product codes.
 *
 * This mapping allows adapting DatasetView column names used by the ERP
 * system to the SDK's ProductIdentifiers value object without modifying
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
final class ProductIdentifiersMapping
{
    public function __construct(
        private readonly ?string $ean = 'EAN',
        private readonly ?string $mpn = 'MPN',
        private readonly ?string $supplierCode = 'KodDodavatele',
    ) {}

    public function getEan(): ?string
    {
        return $this->ean;
    }

    public function getMpn(): ?string
    {
        return $this->mpn;
    }

    public function getSupplierCode(): ?string
    {
        return $this->supplierCode;
    }
}
