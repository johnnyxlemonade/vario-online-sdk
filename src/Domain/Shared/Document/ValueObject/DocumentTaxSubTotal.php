<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\ValueObject;

use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxCalculationMethod;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxScheme;

final class DocumentTaxSubTotal
{
    public function __construct(
        private readonly DocumentTaxCalculationMethod $calculationMethod,
        private readonly DocumentTaxScheme $scheme,
        private readonly float $taxableAmount,
        private readonly float $taxAmount,
        private readonly float $taxPercentage,
        private readonly ?string $taxSchemeExtensionCode = null,
    ) {}

    public function getCalculationMethod(): DocumentTaxCalculationMethod
    {
        return $this->calculationMethod;
    }

    public function getScheme(): DocumentTaxScheme
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
