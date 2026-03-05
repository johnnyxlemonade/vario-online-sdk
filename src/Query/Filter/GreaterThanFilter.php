<?php

declare(strict_types=1);

namespace Lemonade\Vario\Query\Filter;

/**
 * Class GreaterThanFilter
 *
 * Filter using GREATER THAN operator.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Query\Filter
 * @category    Query
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class GreaterThanFilter extends AbstractFilter
{
    public function __construct(
        private readonly string $property,
        private readonly int|float|string $value
    ) {}

    /**
     * @return list<list<array<string,mixed>>>
     */
    public function toArray(): array
    {
        return $this->group([
            'Property' => $this->property,
            'Operator' => Operator::GREATER_THAN->value,
            'Value'    => $this->value,
        ]);
    }
}
