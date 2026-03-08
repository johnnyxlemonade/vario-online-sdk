<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Query\Filter;

use Lemonade\Vario\Query\Filter\BetweenFilter;
use Lemonade\Vario\Query\Filter\Operator;
use PHPUnit\Framework\TestCase;

final class BetweenFilterTest extends TestCase
{
    public function test_to_array_with_numeric_range(): void
    {
        $filter = new BetweenFilter('Price', 10, 20);

        $result = $filter->toArray();

        self::assertSame(
            [
                [
                    [
                        'Property' => 'Price',
                        'Operator' => Operator::GREATER_OR_EQUAL->value,
                        'Value' => 10,
                    ],
                    [
                        'Property' => 'Price',
                        'Operator' => Operator::LESS_OR_EQUAL->value,
                        'Value' => 20,
                    ],
                ],
            ],
            $result
        );
    }

    public function test_to_array_with_string_range(): void
    {
        $filter = new BetweenFilter('Date', '2024-01-01', '2024-12-31');

        $result = $filter->toArray();

        self::assertSame(
            [
                [
                    [
                        'Property' => 'Date',
                        'Operator' => Operator::GREATER_OR_EQUAL->value,
                        'Value' => '2024-01-01',
                    ],
                    [
                        'Property' => 'Date',
                        'Operator' => Operator::LESS_OR_EQUAL->value,
                        'Value' => '2024-12-31',
                    ],
                ],
            ],
            $result
        );
    }
}
