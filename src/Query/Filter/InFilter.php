<?php declare(strict_types=1);

namespace Lemonade\Vario\Query\Filter;

/**
 * Class InFilter
 *
 * Filter using IN operator for matching values in a list.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Query\Filter
 * @category    Query
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class InFilter extends AbstractFilter
{
    /**
     * @param list<string|int|float|bool> $values
     */
    public function __construct(
        private readonly string $property,
        private readonly array $values
    ) {}

    /**
     * @return list<list<array<string,mixed>>>
     */
    public function toArray(): array
    {
        return $this->group([
            'Property' => $this->property,
            'Operator' => Operator::IN->value,
            'Value'    => $this->values,
        ]);
    }
}
