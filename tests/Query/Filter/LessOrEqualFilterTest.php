<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Query\Filter;

use Lemonade\Vario\Query\Filter\LessOrEqualFilter;
use Lemonade\Vario\Query\Filter\Operator;
use PHPUnit\Framework\TestCase;

final class LessOrEqualFilterTest extends TestCase
{
    public function test_to_array_with_numeric_value(): void
    {
        $filter = new LessOrEqualFilter('Price', 50);

        $result = $filter->toArray();

        self::assertSame(
            [
                [
                    [
                        'Property' => 'Price',
                        'Operator' => Operator::LESS_OR_EQUAL->value,
                        'Value' => 50,
                    ],
                ],
            ],
            $result
        );
    }

    public function test_to_array_with_string_value(): void
    {
        $filter = new LessOrEqualFilter('Date', '2024-12-31');

        $result = $filter->toArray();

        self::assertSame(
            [
                [
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
