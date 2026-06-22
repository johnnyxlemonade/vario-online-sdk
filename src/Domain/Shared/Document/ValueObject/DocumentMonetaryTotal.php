<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\ValueObject;

final class DocumentMonetaryTotal
{
    public function __construct(
        private readonly float $payableAmount,
        private readonly float $payableRoundingAmount,
        private readonly float $taxExclusiveAmount,
        private readonly float $taxInclusiveAmount,
    ) {}

    public function getPayableAmount(): float
    {
        return $this->payableAmount;
    }

    public function getPayableRoundingAmount(): float
    {
        return $this->payableRoundingAmount;
    }

    public function getTaxExclusiveAmount(): float
    {
        return $this->taxExclusiveAmount;
    }

    public function getTaxInclusiveAmount(): float
    {
        return $this->taxInclusiveAmount;
    }
}
