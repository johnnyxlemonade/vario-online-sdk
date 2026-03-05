<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\KnownParty;

enum IdentificationScheme: int
{
    case UIN = 0;
    case VAT = 1;
    case GLN = 2;
    case BIC = 3;
    case UUID = 90;
    case ISID = 91;
    case OTHER = 99;
}
