<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\Write;

use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentPriceMode;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxCalculationMethod;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxScheme;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;

/**
 * @template TLineItem of object
 */
final class DocumentCalculatedLineInput
{
    /**
     * @param TLineItem $lineItem
     */
    public function __construct(
        private readonly string $uuid,
        private readonly object $lineItem,
        private readonly float $quantity,
        private readonly string $unitCode,
        private readonly float $unitPrice,
        private readonly float $vatRate,
        private readonly DocumentPriceMode $priceMode = DocumentPriceMode::WithoutVat,
        private readonly DocumentUnitOfMeasureScheme $unitScheme = DocumentUnitOfMeasureScheme::Unknown,
        private readonly ?string $id = null,
        private readonly ?string $note = null,
        private readonly DocumentTaxCalculationMethod $taxCalculationMethod = DocumentTaxCalculationMethod::Add,
        private readonly DocumentTaxScheme $taxScheme = DocumentTaxScheme::Vat,
        private readonly ?string $taxSchemeExtensionCode = null,
    ) {}

    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return TLineItem
     */
    public function getLineItem(): object
    {
        return $this->lineItem;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function getUnitCode(): string
    {
        return $this->unitCode;
    }

    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    public function getVatRate(): float
    {
        return $this->vatRate;
    }

    public function getPriceMode(): DocumentPriceMode
    {
        return $this->priceMode;
    }

    public function getUnitScheme(): DocumentUnitOfMeasureScheme
    {
        return $this->unitScheme;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getTaxCalculationMethod(): DocumentTaxCalculationMethod
    {
        return $this->taxCalculationMethod;
    }

    public function getTaxScheme(): DocumentTaxScheme
    {
        return $this->taxScheme;
    }

    public function getTaxSchemeExtensionCode(): ?string
    {
        return $this->taxSchemeExtensionCode;
    }
}
