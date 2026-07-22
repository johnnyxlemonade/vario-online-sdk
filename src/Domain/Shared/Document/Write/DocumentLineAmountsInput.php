<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\Write;

use InvalidArgumentException;

final class DocumentLineAmountsInput
{
    public function __construct(
        private readonly float $lineExtensionAmount,
        private readonly float $lineExtensionAmountTaxInclusive,
        private readonly ?float $lineAllowanceAmount = null,
    ) {
        if ($lineAllowanceAmount !== null && $lineAllowanceAmount < 0.0) {
            throw new InvalidArgumentException('Line allowance amount must not be negative.');
        }
    }

    public function getLineExtensionAmount(): float
    {
        return $this->lineExtensionAmount;
    }

    public function getLineExtensionAmountTaxInclusive(): float
    {
        return $this->lineExtensionAmountTaxInclusive;
    }

    public function getLineAllowanceAmount(): ?float
    {
        return $this->lineAllowanceAmount;
    }
}
