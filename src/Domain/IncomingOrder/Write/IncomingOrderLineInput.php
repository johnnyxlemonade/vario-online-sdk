<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Write;

use Lemonade\Vario\Domain\Shared\Document\DocumentLineInterface;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentQuantity;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxSubTotal;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentLineAmountsInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentLineIdentityInput;

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
final class IncomingOrderLineInput implements DocumentLineInterface
{
    public function __construct(
        private DocumentLineIdentityInput $identity,
        private DocumentLineAmountsInput $amounts,
        private IncomingOrderLineItemInput $lineItem,
        private DocumentQuantity $lineQuantity,
        private DocumentTaxSubTotal $taxSubTotal,
    ) {}

    public function getUuid(): string
    {
        return $this->identity->getUuid();
    }

    public function withUuid(string $uuid): self
    {
        $this->identity = new DocumentLineIdentityInput(
            uuid: $uuid,
            id: $this->identity->getId(),
            note: $this->identity->getNote(),
        );

        return $this;
    }

    public function getLineExtensionAmount(): float
    {
        return $this->amounts->getLineExtensionAmount();
    }

    public function withLineExtensionAmount(float $lineExtensionAmount): self
    {
        $this->amounts = new DocumentLineAmountsInput(
            lineExtensionAmount: $lineExtensionAmount,
            lineExtensionAmountTaxInclusive: $this->amounts->getLineExtensionAmountTaxInclusive(),
            lineAllowanceAmount: $this->amounts->getLineAllowanceAmount(),
        );

        return $this;
    }

    public function getLineExtensionAmountTaxInclusive(): float
    {
        return $this->amounts->getLineExtensionAmountTaxInclusive();
    }

    public function withLineExtensionAmountTaxInclusive(float $lineExtensionAmountTaxInclusive): self
    {
        $this->amounts = new DocumentLineAmountsInput(
            lineExtensionAmount: $this->amounts->getLineExtensionAmount(),
            lineExtensionAmountTaxInclusive: $lineExtensionAmountTaxInclusive,
            lineAllowanceAmount: $this->amounts->getLineAllowanceAmount(),
        );

        return $this;
    }

    public function getLineAllowanceAmount(): ?float
    {
        return $this->amounts->getLineAllowanceAmount();
    }

    public function withLineAllowanceAmount(?float $lineAllowanceAmount): self
    {
        $this->amounts = new DocumentLineAmountsInput(
            lineExtensionAmount: $this->amounts->getLineExtensionAmount(),
            lineExtensionAmountTaxInclusive: $this->amounts->getLineExtensionAmountTaxInclusive(),
            lineAllowanceAmount: $lineAllowanceAmount,
        );

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

    public function getLineQuantity(): DocumentQuantity
    {
        return $this->lineQuantity;
    }

    public function withLineQuantity(DocumentQuantity $lineQuantity): self
    {
        $this->lineQuantity = $lineQuantity;

        return $this;
    }

    public function getTaxSubTotal(): DocumentTaxSubTotal
    {
        return $this->taxSubTotal;
    }

    public function withTaxSubTotal(DocumentTaxSubTotal $taxSubTotal): self
    {
        $this->taxSubTotal = $taxSubTotal;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->identity->getId();
    }

    public function withId(?string $id): self
    {
        $this->identity = new DocumentLineIdentityInput(
            uuid: $this->identity->getUuid(),
            id: $id,
            note: $this->identity->getNote(),
        );

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->identity->getNote();
    }

    public function withNote(?string $note): self
    {
        $this->identity = new DocumentLineIdentityInput(
            uuid: $this->identity->getUuid(),
            id: $this->identity->getId(),
            note: $note,
        );

        return $this;
    }
}
