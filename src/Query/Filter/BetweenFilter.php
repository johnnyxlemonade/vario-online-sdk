<?php declare(strict_types=1);

namespace Lemonade\Vario\Query\Filter;

/**
 * Class BetweenFilter
 *
 * Filter using BETWEEN operator for range queries.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Query\Filter
 * @category    Query
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class BetweenFilter implements QueryFilterInterface
{
    public function __construct(
        private readonly string $property,
        private readonly int|float|string $from,
        private readonly int|float|string $to
    ) {}

    public function toArray(): array
    {
        return [[
            [
                'Property' => $this->property,
                'Operator' => Operator::GREATER_OR_EQUAL->value,
                'Value'    => $this->from,
            ],
            [
                'Property' => $this->property,
                'Operator' => Operator::LESS_OR_EQUAL->value,
                'Value'    => $this->to,
            ],
        ]];
    }
}
