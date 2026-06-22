<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\Enum;

enum DocumentTaxCalculationMethod: string
{
    case Add = 'Add';
    case Subtract = 'Subtract';
    case Total = 'Total';

    public function toApiValue(): string
    {
        return $this->value;
    }
}
