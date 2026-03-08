<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Query\Filter;

use Lemonade\Vario\Query\Filter\GreaterThanFilter;
use Lemonade\Vario\Query\Filter\Operator;
use PHPUnit\Framework\TestCase;

final class GreaterThanFilterTest extends TestCase
{
    public function test_to_array_with_numeric_value(): void
    {
        $filter = new GreaterThanFilter('Price', 100);

        $result = $filter->toArray();

        self::assertSame(
            [
                [
                    [
                        'Property' => 'Price',
                        'Operator' => Operator::GREATER_THAN->value,
                        'Value' => 100,
                    ],
                ],
            ],
            $result
        );
    }

    public function test_to_array_with_string_value(): void
    {
        $filter = new GreaterThanFilter('Date', '2024-01-01');

        $result = $filter->toArray();

        self::assertSame(
            [
                [
                    [
                        'Property' => 'Date',
                        'Operator' => Operator::GREATER_THAN->value,
                        'Value' => '2024-01-01',
                    ],
                ],
            ],
            $result
        );
    }
}
