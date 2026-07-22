<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Write;

use InvalidArgumentException;
use Lemonade\Vario\Domain\Shared\Document\DocumentLineInterface;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentQuantity;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxSubTotal;

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
        private string $uuid,
        private float $lineExtensionAmount,
        private float $lineExtensionAmountTaxInclusive,
        private IncomingOrderLineItemInput $lineItem,
        private DocumentQuantity $lineQuantity,
        private DocumentTaxSubTotal $taxSubTotal,
        private ?float $lineAllowanceAmount = null,
        private ?string $id = null,
        private ?string $note = null,
    ) {
        $this->assertLineAllowanceAmount($lineAllowanceAmount);
    }

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

    public function getLineAllowanceAmount(): ?float
    {
        return $this->lineAllowanceAmount;
    }

    public function withLineAllowanceAmount(?float $lineAllowanceAmount): self
    {
        $this->assertLineAllowanceAmount($lineAllowanceAmount);
        $this->lineAllowanceAmount = $lineAllowanceAmount;

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

    private function assertLineAllowanceAmount(?float $lineAllowanceAmount): void
    {
        if ($lineAllowanceAmount !== null && $lineAllowanceAmount < 0.0) {
            throw new InvalidArgumentException('Line allowance amount must not be negative.');
        }
    }
}
