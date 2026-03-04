<?php declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\ValueObject;

/**
 * Class ProductIdentifiers
 *
 * Additional product identifiers used in integrations
 * and external catalogues.
 *
 * These identifiers complement the internal product identity
 * (UUID, SKU) and are commonly used in logistics, marketplaces,
 * and supplier integrations.
 *
 * Typical examples include EAN (barcode), MPN (manufacturer
 * part number), and supplier product codes.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class ProductIdentifiers
{
    public function __construct(
        private readonly ?string $ean,
        private readonly ?string $mpn,
        private readonly ?string $supplierCode,
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

    /**
     * @return array{
     *     ean: ?string,
     *     mpn: ?string,
     *     supplierCode: ?string
     * }
     */
    public function toArray(): array
    {
        return [
            'ean' => $this->ean,
            'mpn' => $this->mpn,
            'supplierCode' => $this->supplierCode,
        ];
    }
}
