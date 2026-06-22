<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\OutgoingQuotation\Write;

use Lemonade\Vario\Domain\OutgoingQuotation\ValueObject\OutgoingQuotationQuantity;
use Lemonade\Vario\Domain\OutgoingQuotation\ValueObject\OutgoingQuotationTaxSubTotal;

final class OutgoingQuotationLineInput
{
    public function __construct(
        private readonly string $uuid,
        private readonly float $lineExtensionAmount,
        private readonly float $lineExtensionAmountTaxInclusive,
        private readonly OutgoingQuotationLineItemInput $lineItem,
        private readonly OutgoingQuotationQuantity $lineQuantity,
        private readonly OutgoingQuotationTaxSubTotal $taxSubTotal,
        private readonly ?string $id = null,
        private readonly ?string $note = null,
    ) {}

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function withUuid(string $uuid): self
    {
        return new self(
            uuid: $uuid,
            lineExtensionAmount: $this->lineExtensionAmount,
            lineExtensionAmountTaxInclusive: $this->lineExtensionAmountTaxInclusive,
            lineItem: $this->lineItem,
            lineQuantity: $this->lineQuantity,
            taxSubTotal: $this->taxSubTotal,
            id: $this->id,
            note: $this->note,
        );
    }

    public function getLineExtensionAmount(): float
    {
        return $this->lineExtensionAmount;
    }

    public function withLineExtensionAmount(float $lineExtensionAmount): self
    {
        return new self(
            uuid: $this->uuid,
            lineExtensionAmount: $lineExtensionAmount,
            lineExtensionAmountTaxInclusive: $this->lineExtensionAmountTaxInclusive,
            lineItem: $this->lineItem,
            lineQuantity: $this->lineQuantity,
            taxSubTotal: $this->taxSubTotal,
            id: $this->id,
            note: $this->note,
        );
    }

    public function getLineExtensionAmountTaxInclusive(): float
    {
        return $this->lineExtensionAmountTaxInclusive;
    }

    public function withLineExtensionAmountTaxInclusive(float $lineExtensionAmountTaxInclusive): self
    {
        return new self(
            uuid: $this->uuid,
            lineExtensionAmount: $this->lineExtensionAmount,
            lineExtensionAmountTaxInclusive: $lineExtensionAmountTaxInclusive,
            lineItem: $this->lineItem,
            lineQuantity: $this->lineQuantity,
            taxSubTotal: $this->taxSubTotal,
            id: $this->id,
            note: $this->note,
        );
    }

    public function getLineItem(): OutgoingQuotationLineItemInput
    {
        return $this->lineItem;
    }

    public function withLineItem(OutgoingQuotationLineItemInput $lineItem): self
    {
        return new self(
            uuid: $this->uuid,
            lineExtensionAmount: $this->lineExtensionAmount,
            lineExtensionAmountTaxInclusive: $this->lineExtensionAmountTaxInclusive,
            lineItem: $lineItem,
            lineQuantity: $this->lineQuantity,
            taxSubTotal: $this->taxSubTotal,
            id: $this->id,
            note: $this->note,
        );
    }

    public function getLineQuantity(): OutgoingQuotationQuantity
    {
        return $this->lineQuantity;
    }

    public function withLineQuantity(OutgoingQuotationQuantity $lineQuantity): self
    {
        return new self(
            uuid: $this->uuid,
            lineExtensionAmount: $this->lineExtensionAmount,
            lineExtensionAmountTaxInclusive: $this->lineExtensionAmountTaxInclusive,
            lineItem: $this->lineItem,
            lineQuantity: $lineQuantity,
            taxSubTotal: $this->taxSubTotal,
            id: $this->id,
            note: $this->note,
        );
    }

    public function getTaxSubTotal(): OutgoingQuotationTaxSubTotal
    {
        return $this->taxSubTotal;
    }

    public function withTaxSubTotal(OutgoingQuotationTaxSubTotal $taxSubTotal): self
    {
        return new self(
            uuid: $this->uuid,
            lineExtensionAmount: $this->lineExtensionAmount,
            lineExtensionAmountTaxInclusive: $this->lineExtensionAmountTaxInclusive,
            lineItem: $this->lineItem,
            lineQuantity: $this->lineQuantity,
            taxSubTotal: $taxSubTotal,
            id: $this->id,
            note: $this->note,
        );
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function withId(?string $id): self
    {
        return new self(
            uuid: $this->uuid,
            lineExtensionAmount: $this->lineExtensionAmount,
            lineExtensionAmountTaxInclusive: $this->lineExtensionAmountTaxInclusive,
            lineItem: $this->lineItem,
            lineQuantity: $this->lineQuantity,
            taxSubTotal: $this->taxSubTotal,
            id: $id,
            note: $this->note,
        );
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function withNote(?string $note): self
    {
        return new self(
            uuid: $this->uuid,
            lineExtensionAmount: $this->lineExtensionAmount,
            lineExtensionAmountTaxInclusive: $this->lineExtensionAmountTaxInclusive,
            lineItem: $this->lineItem,
            lineQuantity: $this->lineQuantity,
            taxSubTotal: $this->taxSubTotal,
            id: $this->id,
            note: $note,
        );
    }
}
