<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\Enum;

enum DocumentUnitOfMeasureScheme: string
{
    case Unknown = 'Unknown';
    case SI = 'SI';
    case ImperialGB = 'ImperialGB';
    case ImperialUS = 'ImperialUS';

    public function toApiValue(): string
    {
        return $this->value;
    }
}
