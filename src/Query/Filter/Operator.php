<?php declare(strict_types=1);

namespace Lemonade\Vario\Query\Filter;

enum Operator: int
{
    case EQUALS = 6;
    case NOT_EQUALS = 7;

    case LESS_THAN = 8;
    case LESS_OR_EQUAL = 9;

    case GREATER_THAN = 10;
    case GREATER_OR_EQUAL = 11;

    case STARTS_WITH = 12;
    case CONTAINS = 13;
    case ENDS_WITH = 14;

    case LIKE = 18;
    case NOT_LIKE = 19;
}
