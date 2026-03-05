<?php

declare(strict_types=1);

namespace Lemonade\Vario\Query\Filter;

/**
 * Class LikeFilter
 *
 * Filter using LIKE operator for partial text matching.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Query\Filter
 * @category    Query
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class LikeFilter extends AbstractFilter
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
            'Operator' => Operator::LIKE->value,
            'Value'    => $this->value,
        ]);
    }
}
