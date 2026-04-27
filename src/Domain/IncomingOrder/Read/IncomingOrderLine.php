<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Read;

use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderQuantity;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderTaxSubTotal;

/**
 * Class IncomingOrderLine
 *
 * Immutable domain read model representing a single document line
 * inside an IncomingOrder.
 *
 * Instances of this class are created by IncomingOrderMapper when
 * converting raw Vario API payloads into strongly-typed domain objects.
 *
 * The model intentionally exposes stable core properties such as:
 *
 * - line UUID / ID
 * - line totals
 * - line item identification
 * - quantity
 * - tax subtotal
 * - unit price
 * - note
 *
 * Any additional fields returned by the API are preserved in the
 * `$extra` payload to maintain forward compatibility.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderLine
{
    /**
     * @param array<string,mixed> $extra Additional unmapped API fields.
     */
    public function __construct(
        // =========================
        // ENTITY IDENTITY (optional)
        // =========================
        private readonly ?string $uuid = null,
        private readonly ?string $id = null,

        // =========================
        // CORE ATTRIBUTES (optional)
        // =========================
        private readonly ?float $lineExtensionAmount = null,
        private readonly ?float $lineExtensionAmountTaxInclusive = null,
        private readonly ?string $note = null,

        // =========================
        // VALUE OBJECTS
        // =========================
        private readonly ?IncomingOrderLineItem $lineItem = null,
        private readonly ?IncomingOrderQuantity $lineQuantity = null,
        private readonly ?IncomingOrderTaxSubTotal $taxSubTotal = null,
        private readonly ?IncomingOrderUnitPrice $unitPrice = null,

        // =========================
        // FORWARD COMPATIBILITY
        // =========================
        private readonly array $extra = [],
    ) {}

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getLineExtensionAmount(): ?float
    {
        return $this->lineExtensionAmount;
    }

    public function getLineExtensionAmountTaxInclusive(): ?float
    {
        return $this->lineExtensionAmountTaxInclusive;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getLineItem(): ?IncomingOrderLineItem
    {
        return $this->lineItem;
    }

    public function getLineQuantity(): ?IncomingOrderQuantity
    {
        return $this->lineQuantity;
    }

    public function getTaxSubTotal(): ?IncomingOrderTaxSubTotal
    {
        return $this->taxSubTotal;
    }

    public function getUnitPrice(): ?IncomingOrderUnitPrice
    {
        return $this->unitPrice;
    }

    public function hasLineItem(): bool
    {
        return $this->lineItem !== null;
    }

    public function hasQuantity(): bool
    {
        return $this->lineQuantity !== null;
    }

    public function hasTaxSubTotal(): bool
    {
        return $this->taxSubTotal !== null;
    }

    public function hasUnitPrice(): bool
    {
        return $this->unitPrice !== null;
    }

    /**
     * Returns additional API fields not mapped to explicit properties.
     *
     * @return array<string,mixed>
     */
    public function getExtra(): array
    {
        return $this->extra;
    }
}
