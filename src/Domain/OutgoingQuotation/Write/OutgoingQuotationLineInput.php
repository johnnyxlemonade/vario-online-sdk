<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\OutgoingQuotation\Write;

use InvalidArgumentException;
use Lemonade\Vario\Domain\Shared\Document\DocumentLineInterface;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentQuantity;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxSubTotal;

final class OutgoingQuotationLineInput implements DocumentLineInterface
{
    public function __construct(
        private readonly string $uuid,
        private readonly float $lineExtensionAmount,
        private readonly float $lineExtensionAmountTaxInclusive,
        private readonly OutgoingQuotationLineItemInput $lineItem,
        private readonly DocumentQuantity $lineQuantity,
        private readonly DocumentTaxSubTotal $taxSubTotal,
        private readonly ?float $lineAllowanceAmount = null,
        private readonly ?string $id = null,
        private readonly ?string $note = null,
    ) {
        $this->assertLineAllowanceAmount($lineAllowanceAmount);
    }

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
            lineAllowanceAmount: $this->lineAllowanceAmount,
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
            lineAllowanceAmount: $this->lineAllowanceAmount,
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
            lineAllowanceAmount: $this->lineAllowanceAmount,
            id: $this->id,
            note: $this->note,
        );
    }

    public function getLineAllowanceAmount(): ?float
    {
        return $this->lineAllowanceAmount;
    }

    public function withLineAllowanceAmount(?float $lineAllowanceAmount): self
    {
        return new self(
            uuid: $this->uuid,
            lineExtensionAmount: $this->lineExtensionAmount,
            lineExtensionAmountTaxInclusive: $this->lineExtensionAmountTaxInclusive,
            lineItem: $this->lineItem,
            lineQuantity: $this->lineQuantity,
            taxSubTotal: $this->taxSubTotal,
            lineAllowanceAmount: $lineAllowanceAmount,
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
            lineAllowanceAmount: $this->lineAllowanceAmount,
            id: $this->id,
            note: $this->note,
        );
    }

    public function getLineQuantity(): DocumentQuantity
    {
        return $this->lineQuantity;
    }

    public function withLineQuantity(DocumentQuantity $lineQuantity): self
    {
        return new self(
            uuid: $this->uuid,
            lineExtensionAmount: $this->lineExtensionAmount,
            lineExtensionAmountTaxInclusive: $this->lineExtensionAmountTaxInclusive,
            lineItem: $this->lineItem,
            lineQuantity: $lineQuantity,
            taxSubTotal: $this->taxSubTotal,
            lineAllowanceAmount: $this->lineAllowanceAmount,
            id: $this->id,
            note: $this->note,
        );
    }

    public function getTaxSubTotal(): DocumentTaxSubTotal
    {
        return $this->taxSubTotal;
    }

    public function withTaxSubTotal(DocumentTaxSubTotal $taxSubTotal): self
    {
        return new self(
            uuid: $this->uuid,
            lineExtensionAmount: $this->lineExtensionAmount,
            lineExtensionAmountTaxInclusive: $this->lineExtensionAmountTaxInclusive,
            lineItem: $this->lineItem,
            lineQuantity: $this->lineQuantity,
            taxSubTotal: $taxSubTotal,
            lineAllowanceAmount: $this->lineAllowanceAmount,
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
            lineAllowanceAmount: $this->lineAllowanceAmount,
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
            lineAllowanceAmount: $this->lineAllowanceAmount,
            id: $this->id,
            note: $note,
        );
    }

    private function assertLineAllowanceAmount(?float $lineAllowanceAmount): void
    {
        if ($lineAllowanceAmount !== null && $lineAllowanceAmount < 0.0) {
            throw new InvalidArgumentException('Line allowance amount must not be negative.');
        }
    }
}
