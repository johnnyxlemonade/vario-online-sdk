<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\Write;

use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxCalculationMethod;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxScheme;

final class DocumentCalculatedLineTaxInput
{
    public function __construct(
        private readonly DocumentTaxCalculationMethod $calculationMethod = DocumentTaxCalculationMethod::Add,
        private readonly DocumentTaxScheme $scheme = DocumentTaxScheme::Vat,
        private readonly ?string $schemeExtensionCode = null,
    ) {}

    public function getCalculationMethod(): DocumentTaxCalculationMethod
    {
        return $this->calculationMethod;
    }

    public function getScheme(): DocumentTaxScheme
    {
        return $this->scheme;
    }

    public function getSchemeExtensionCode(): ?string
    {
        return $this->schemeExtensionCode;
    }
}
