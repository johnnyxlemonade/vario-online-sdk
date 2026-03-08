<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Query\Filter;

use Lemonade\Vario\Query\Filter\InFilter;
use Lemonade\Vario\Query\Filter\Operator;
use PHPUnit\Framework\TestCase;

final class InFilterTest extends TestCase
{
    public function test_to_array_generates_filter_for_each_value(): void
    {
        $filter = new InFilter('Id', [1, 2, 3]);

        $result = $filter->toArray();

        self::assertSame(
            [
                [[
                    'Property' => 'Id',
                    'Operator' => Operator::EQUALS->value,
                    'Value' => 1,
                ]],
                [[
                    'Property' => 'Id',
                    'Operator' => Operator::EQUALS->value,
                    'Value' => 2,
                ]],
                [[
                    'Property' => 'Id',
                    'Operator' => Operator::EQUALS->value,
                    'Value' => 3,
                ]],
            ],
            $result
        );
    }

    public function test_empty_values_returns_empty_array(): void
    {
        $filter = new InFilter('Id', []);

        $result = $filter->toArray();

        self::assertSame([], $result);
    }
}
