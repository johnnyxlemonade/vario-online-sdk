<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\Write;

use InvalidArgumentException;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentPriceMode;

final class DocumentCalculatedLinePriceInput
{
    public function __construct(
        private readonly float $unitPrice,
        private readonly float $vatRate,
        private readonly DocumentPriceMode $priceMode = DocumentPriceMode::WithoutVat,
        private readonly ?float $lineAllowanceAmount = null,
    ) {
        if ($lineAllowanceAmount !== null && $lineAllowanceAmount < 0.0) {
            throw new InvalidArgumentException('Line allowance amount must not be negative.');
        }
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

    public function getLineAllowanceAmount(): ?float
    {
        return $this->lineAllowanceAmount;
    }
}
