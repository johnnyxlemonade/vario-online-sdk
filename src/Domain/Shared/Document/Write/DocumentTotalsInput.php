<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\Write;

use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentMonetaryTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxExchangeRate;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxTotal;

final class DocumentTotalsInput
{
    public function __construct(
        private readonly DocumentMonetaryTotal $monetaryTotal,
        private readonly DocumentTaxExchangeRate $taxExchangeRate,
        private readonly DocumentTaxTotal $taxTotal,
    ) {}

    public function getMonetaryTotal(): DocumentMonetaryTotal
    {
        return $this->monetaryTotal;
    }

    public function getTaxExchangeRate(): DocumentTaxExchangeRate
    {
        return $this->taxExchangeRate;
    }

    public function getTaxTotal(): DocumentTaxTotal
    {
        return $this->taxTotal;
    }
}
