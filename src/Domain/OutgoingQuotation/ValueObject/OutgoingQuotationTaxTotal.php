<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\OutgoingQuotation\ValueObject;

final class OutgoingQuotationTaxTotal
{
    /**
     * @param list<OutgoingQuotationTaxSubTotal> $taxSubTotals
     */
    public function __construct(
        private readonly float $taxAmount,
        private readonly array $taxSubTotals,
    ) {}

    public function getTaxAmount(): float
    {
        return $this->taxAmount;
    }

    /**
     * @return list<OutgoingQuotationTaxSubTotal>
     */
    public function getTaxSubTotals(): array
    {
        return $this->taxSubTotals;
    }
}
