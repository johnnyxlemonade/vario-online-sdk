<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\OutgoingQuotation\Enum;

enum OutgoingQuotationUnitOfMeasureScheme: string
{
    case Unknown = 'Unknown';
    case SI = 'SI';

    public function toApiValue(): string
    {
        return $this->value;
    }
}
