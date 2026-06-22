<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\Enum;

enum DocumentTaxScheme: string
{
    case NoTax = 'NoTax';
    case Vat = 'Vat';
    case VatReverseCharge = 'VatReverseCharge';

    public function toApiValue(): string
    {
        return $this->value;
    }
}
