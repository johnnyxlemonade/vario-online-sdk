<?php

declare(strict_types=1);

namespace Lemonade\Vario\Query\Filter;

/**
 * Class StartsWithFilter
 *
 * Filter using STARTS WITH operator.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Query\Filter
 * @category    Query
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class StartsWithFilter extends AbstractFilter
{
    public function __construct(
        private readonly string $property,
        private readonly string $value
    ) {}

    /**
     * @return list<list<array<string,mixed>>>
     */
    public function toArray(): array
    {
        return $this->group([
            'Property' => $this->property,
            'Operator' => Operator::STARTS_WITH->value,
            'Value'    => $this->value,
        ]);
    }
}
