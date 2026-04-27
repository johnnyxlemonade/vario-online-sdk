<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Write;

use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderQuantity;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderTaxSubTotal;

/**
 * Class IncomingOrderLineInput
 *
 * Mutable write model for a single IncomingOrder document line.
 *
 * This object represents one item row in the Vario IncomingOrder payload.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderLineInput
{
    public function __construct(
        private string $uuid,
        private float $lineExtensionAmount,
        private float $lineExtensionAmountTaxInclusive,
        private IncomingOrderLineItemInput $lineItem,
        private IncomingOrderQuantity $lineQuantity,
        private IncomingOrderTaxSubTotal $taxSubTotal,
        private ?string $id = null,
        private ?string $note = null,
    ) {}

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function withUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getLineExtensionAmount(): float
    {
        return $this->lineExtensionAmount;
    }

    public function withLineExtensionAmount(float $lineExtensionAmount): self
    {
        $this->lineExtensionAmount = $lineExtensionAmount;

        return $this;
    }

    public function getLineExtensionAmountTaxInclusive(): float
    {
        return $this->lineExtensionAmountTaxInclusive;
    }

    public function withLineExtensionAmountTaxInclusive(float $lineExtensionAmountTaxInclusive): self
    {
        $this->lineExtensionAmountTaxInclusive = $lineExtensionAmountTaxInclusive;

        return $this;
    }

    public function getLineItem(): IncomingOrderLineItemInput
    {
        return $this->lineItem;
    }

    public function withLineItem(IncomingOrderLineItemInput $lineItem): self
    {
        $this->lineItem = $lineItem;

        return $this;
    }

    public function getLineQuantity(): IncomingOrderQuantity
    {
        return $this->lineQuantity;
    }

    public function withLineQuantity(IncomingOrderQuantity $lineQuantity): self
    {
        $this->lineQuantity = $lineQuantity;

        return $this;
    }

    public function getTaxSubTotal(): IncomingOrderTaxSubTotal
    {
        return $this->taxSubTotal;
    }

    public function withTaxSubTotal(IncomingOrderTaxSubTotal $taxSubTotal): self
    {
        $this->taxSubTotal = $taxSubTotal;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function withId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function withNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
