<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\OutgoingQuotation\Enum;

enum OutgoingQuotationTaxCalculationMethod: string
{
    case Add = 'Add';
    case Total = 'Total';

    public function toApiValue(): string
    {
        return $this->value;
    }
}
