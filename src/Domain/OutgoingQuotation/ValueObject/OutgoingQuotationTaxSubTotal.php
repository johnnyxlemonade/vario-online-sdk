<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\OutgoingQuotation\ValueObject;

use Lemonade\Vario\Domain\OutgoingQuotation\Enum\OutgoingQuotationTaxCalculationMethod;
use Lemonade\Vario\Domain\OutgoingQuotation\Enum\OutgoingQuotationTaxScheme;

final class OutgoingQuotationTaxSubTotal
{
    public function __construct(
        private readonly OutgoingQuotationTaxCalculationMethod $calculationMethod,
        private readonly OutgoingQuotationTaxScheme $scheme,
        private readonly float $taxableAmount,
        private readonly float $taxAmount,
        private readonly float $taxPercentage,
        private readonly ?string $taxSchemeExtensionCode = null,
    ) {}

    public function getCalculationMethod(): OutgoingQuotationTaxCalculationMethod
    {
        return $this->calculationMethod;
    }

    public function getScheme(): OutgoingQuotationTaxScheme
    {
        return $this->scheme;
    }

    public function getTaxableAmount(): float
    {
        return $this->taxableAmount;
    }

    public function getTaxAmount(): float
    {
        return $this->taxAmount;
    }

    public function getTaxPercentage(): float
    {
        return $this->taxPercentage;
    }

    public function getTaxSchemeExtensionCode(): ?string
    {
        return $this->taxSchemeExtensionCode;
    }
}
