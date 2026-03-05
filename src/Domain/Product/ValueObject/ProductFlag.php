<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\ValueObject;

enum ProductFlag: string
{
    case SALE = 'sale';
    case NEW = 'new';
    case DISCOUNT = 'discount';
    case CLEARANCE = 'clearance';
    case RECOMMENDED = 'recommended';
    case PREPARING = 'preparing';
}
