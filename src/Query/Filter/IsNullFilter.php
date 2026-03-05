<?php

declare(strict_types=1);

namespace Lemonade\Vario\Query\Filter;

/**
 * Class IsNullFilter
 *
 * Filter using IS NULL operator.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Query\Filter
 * @category    Query
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IsNullFilter implements QueryFilterInterface
{
    public function __construct(
        private readonly string $property
    ) {}

    public function toArray(): array
    {
        return [[
            [
                'Property' => $this->property,
                'Operator' => Operator::EQUALS->value,
                'Value'    => null,
            ],
        ]];
    }
}
