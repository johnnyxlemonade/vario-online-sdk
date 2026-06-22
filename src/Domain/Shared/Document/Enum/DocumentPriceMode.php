<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\Enum;

enum DocumentPriceMode: string
{
    case WithoutVat = 'WithoutVat';
    case WithVat = 'WithVat';

    public function toApiValue(): string
    {
        return $this->value;
    }
}
