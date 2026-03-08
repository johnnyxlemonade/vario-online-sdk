<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Query\Filter;

use Lemonade\Vario\Query\Filter\EqualsFilter;
use Lemonade\Vario\Query\Filter\Operator;
use PHPUnit\Framework\TestCase;

final class EqualsFilterTest extends TestCase
{
    public function test_to_array_with_string(): void
    {
        $filter = new EqualsFilter('Name', 'John');

        $result = $filter->toArray();

        self::assertSame(
            [
                [
                    [
                        'Property' => 'Name',
                        'Operator' => Operator::EQUALS->value,
                        'Value' => 'John',
                    ],
                ],
            ],
            $result
        );
    }

    public function test_to_array_with_integer(): void
    {
        $filter = new EqualsFilter('Age', 30);

        $result = $filter->toArray();

        self::assertSame(
            [
                [
                    [
                        'Property' => 'Age',
                        'Operator' => Operator::EQUALS->value,
                        'Value' => 30,
                    ],
                ],
            ],
            $result
        );
    }

    public function test_to_array_with_boolean(): void
    {
        $filter = new EqualsFilter('Active', true);

        $result = $filter->toArray();

        self::assertSame(
            [
                [
                    [
                        'Property' => 'Active',
                        'Operator' => Operator::EQUALS->value,
                        'Value' => true,
                    ],
                ],
            ],
            $result
        );
    }
}
