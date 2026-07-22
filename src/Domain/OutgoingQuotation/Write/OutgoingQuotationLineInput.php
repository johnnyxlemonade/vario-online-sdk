<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\OutgoingQuotation\Write;

use Lemonade\Vario\Domain\Shared\Document\DocumentLineInterface;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentQuantity;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxSubTotal;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentLineAmountsInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentLineIdentityInput;

final class OutgoingQuotationLineInput implements DocumentLineInterface
{
    public function __construct(
        private readonly DocumentLineIdentityInput $identity,
        private readonly DocumentLineAmountsInput $amounts,
        private readonly OutgoingQuotationLineItemInput $lineItem,
        private readonly DocumentQuantity $lineQuantity,
        private readonly DocumentTaxSubTotal $taxSubTotal,
    ) {}

    public function getUuid(): string
    {
        return $this->identity->getUuid();
    }

    public function withUuid(string $uuid): self
    {
        return new self(
            identity: new DocumentLineIdentityInput(
                uuid: $uuid,
                id: $this->identity->getId(),
                note: $this->identity->getNote(),
            ),
            amounts: $this->amounts,
            lineItem: $this->lineItem,
            lineQuantity: $this->lineQuantity,
            taxSubTotal: $this->taxSubTotal,
        );
    }

    public function getLineExtensionAmount(): float
    {
        return $this->amounts->getLineExtensionAmount();
    }

    public function withLineExtensionAmount(float $lineExtensionAmount): self
    {
        return new self(
            identity: $this->identity,
            amounts: new DocumentLineAmountsInput(
                lineExtensionAmount: $lineExtensionAmount,
                lineExtensionAmountTaxInclusive: $this->amounts->getLineExtensionAmountTaxInclusive(),
                lineAllowanceAmount: $this->amounts->getLineAllowanceAmount(),
            ),
            lineItem: $this->lineItem,
            lineQuantity: $this->lineQuantity,
            taxSubTotal: $this->taxSubTotal,
        );
    }

    public function getLineExtensionAmountTaxInclusive(): float
    {
        return $this->amounts->getLineExtensionAmountTaxInclusive();
    }

    public function withLineExtensionAmountTaxInclusive(float $lineExtensionAmountTaxInclusive): self
    {
        return new self(
            identity: $this->identity,
            amounts: new DocumentLineAmountsInput(
                lineExtensionAmount: $this->amounts->getLineExtensionAmount(),
                lineExtensionAmountTaxInclusive: $lineExtensionAmountTaxInclusive,
                lineAllowanceAmount: $this->amounts->getLineAllowanceAmount(),
            ),
            lineItem: $this->lineItem,
            lineQuantity: $this->lineQuantity,
            taxSubTotal: $this->taxSubTotal,
        );
    }

    public function getLineAllowanceAmount(): ?float
    {
        return $this->amounts->getLineAllowanceAmount();
    }

    public function withLineAllowanceAmount(?float $lineAllowanceAmount): self
    {
        return new self(
            identity: $this->identity,
            amounts: new DocumentLineAmountsInput(
                lineExtensionAmount: $this->amounts->getLineExtensionAmount(),
                lineExtensionAmountTaxInclusive: $this->amounts->getLineExtensionAmountTaxInclusive(),
                lineAllowanceAmount: $lineAllowanceAmount,
            ),
            lineItem: $this->lineItem,
            lineQuantity: $this->lineQuantity,
            taxSubTotal: $this->taxSubTotal,
        );
    }

    public function getLineItem(): OutgoingQuotationLineItemInput
    {
        return $this->lineItem;
    }

    public function withLineItem(OutgoingQuotationLineItemInput $lineItem): self
    {
        return new self(
            identity: $this->identity,
            amounts: $this->amounts,
            lineItem: $lineItem,
            lineQuantity: $this->lineQuantity,
            taxSubTotal: $this->taxSubTotal,
        );
    }

    public function getLineQuantity(): DocumentQuantity
    {
        return $this->lineQuantity;
    }

    public function withLineQuantity(DocumentQuantity $lineQuantity): self
    {
        return new self(
            identity: $this->identity,
            amounts: $this->amounts,
            lineItem: $this->lineItem,
            lineQuantity: $lineQuantity,
            taxSubTotal: $this->taxSubTotal,
        );
    }

    public function getTaxSubTotal(): DocumentTaxSubTotal
    {
        return $this->taxSubTotal;
    }

    public function withTaxSubTotal(DocumentTaxSubTotal $taxSubTotal): self
    {
        return new self(
            identity: $this->identity,
            amounts: $this->amounts,
            lineItem: $this->lineItem,
            lineQuantity: $this->lineQuantity,
            taxSubTotal: $taxSubTotal,
        );
    }

    public function getId(): ?string
    {
        return $this->identity->getId();
    }

    public function withId(?string $id): self
    {
        return new self(
            identity: new DocumentLineIdentityInput(
                uuid: $this->identity->getUuid(),
                id: $id,
                note: $this->identity->getNote(),
            ),
            amounts: $this->amounts,
            lineItem: $this->lineItem,
            lineQuantity: $this->lineQuantity,
            taxSubTotal: $this->taxSubTotal,
        );
    }

    public function getNote(): ?string
    {
        return $this->identity->getNote();
    }

    public function withNote(?string $note): self
    {
        return new self(
            identity: new DocumentLineIdentityInput(
                uuid: $this->identity->getUuid(),
                id: $this->identity->getId(),
                note: $note,
            ),
            amounts: $this->amounts,
            lineItem: $this->lineItem,
            lineQuantity: $this->lineQuantity,
            taxSubTotal: $this->taxSubTotal,
        );
    }
}
