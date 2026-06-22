<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\OutgoingQuotation\Enum;

enum OutgoingQuotationTaxScheme: string
{
    case Vat = 'Vat';

    public function toApiValue(): string
    {
        return $this->value;
    }
}
