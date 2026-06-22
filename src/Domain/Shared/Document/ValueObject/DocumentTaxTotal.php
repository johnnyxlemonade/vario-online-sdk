<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\ValueObject;

final class DocumentTaxTotal
{
    /**
     * @param list<DocumentTaxSubTotal> $taxSubTotals
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
     * @return list<DocumentTaxSubTotal>
     */
    public function getTaxSubTotals(): array
    {
        return $this->taxSubTotals;
    }
}
