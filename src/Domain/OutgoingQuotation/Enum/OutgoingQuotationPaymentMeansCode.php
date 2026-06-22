<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\OutgoingQuotation\Enum;

enum OutgoingQuotationPaymentMeansCode: string
{
    case BankAccount = 'BankAccount';
    case Cash = 'Cash';
    case Cheque = 'Cheque';

    public function toApiValue(): string
    {
        return $this->value;
    }
}
