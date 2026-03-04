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
final class InFilter implements QueryFilterInterface
{
    /**
     * @param list<string|int|float|bool> $values
     */
    public function __construct(
        private readonly string $property,
        private readonly array $values
    ) {}

    public function toArray(): array
    {
        $result = [];

        foreach ($this->values as $value) {
            $result[] = [[
                'Property' => $this->property,
                'Operator' => Operator::EQUALS->value,
                'Value'    => $value,
            ]];
        }

        return $result;
    }
}
