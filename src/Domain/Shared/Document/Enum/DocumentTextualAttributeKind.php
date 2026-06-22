<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\Enum;

enum DocumentTextualAttributeKind: string
{
    case FreeText = 'FreeText';
    case ExtendedID = 'ExtendedID';
    case BarcodeSymbologyID = 'BarcodeSymbologyID';

    public function toApiValue(): string
    {
        return $this->value;
    }
}
