<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\Write;

use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentPriceMode;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxCalculationMethod;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxScheme;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;

/**
 * @template TLineItem of DocumentCalculatedLineItemInputInterface
 */
final class DocumentCalculatedLineInput
{
    /**
     * @param TLineItem $lineItem
     */
    public function __construct(
        private readonly DocumentCalculatedLineIdentityInput $identity,
        private readonly DocumentCalculatedLineItemInputInterface $lineItem,
        private readonly DocumentCalculatedLineQuantityInput $quantity,
        private readonly DocumentCalculatedLinePriceInput $price,
        private readonly DocumentCalculatedLineTaxInput $tax = new DocumentCalculatedLineTaxInput(),
    ) {}

    public function getUuid(): string
    {
        return $this->identity->getUuid();
    }

    /**
     * @return TLineItem
     */
    public function getLineItem(): DocumentCalculatedLineItemInputInterface
    {
        return $this->lineItem;
    }

    public function getQuantity(): float
    {
        return $this->quantity->getValue();
    }

    public function getUnitCode(): string
    {
        return $this->quantity->getUnitCode();
    }

    public function getUnitPrice(): float
    {
        return $this->price->getUnitPrice();
    }

    public function getVatRate(): float
    {
        return $this->price->getVatRate();
    }

    public function getPriceMode(): DocumentPriceMode
    {
        return $this->price->getPriceMode();
    }

    public function getUnitScheme(): DocumentUnitOfMeasureScheme
    {
        return $this->quantity->getScheme();
    }

    public function getId(): ?string
    {
        return $this->identity->getId();
    }

    public function getNote(): ?string
    {
        return $this->identity->getNote();
    }

    public function getTaxCalculationMethod(): DocumentTaxCalculationMethod
    {
        return $this->tax->getCalculationMethod();
    }

    public function getTaxScheme(): DocumentTaxScheme
    {
        return $this->tax->getScheme();
    }

    public function getTaxSchemeExtensionCode(): ?string
    {
        return $this->tax->getSchemeExtensionCode();
    }

    public function getLineAllowanceAmount(): ?float
    {
        return $this->price->getLineAllowanceAmount();
    }
}
