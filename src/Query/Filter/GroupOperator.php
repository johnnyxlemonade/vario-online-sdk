<?php

declare(strict_types=1);

namespace Lemonade\Vario\Query\Filter;

/**
 * Logical operator used for grouping filters.
 */
enum GroupOperator: string
{
    case AND = 'AND';
    case OR  = 'OR';
}
