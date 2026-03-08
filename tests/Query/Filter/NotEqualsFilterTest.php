<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Query\Filter;

use Lemonade\Vario\Query\Filter\NotEqualsFilter;
use Lemonade\Vario\Query\Filter\Operator;
use PHPUnit\Framework\TestCase;

final class NotEqualsFilterTest extends TestCase
{
    public function test_to_array_with_string(): void
    {
        $filter = new NotEqualsFilter('Name', 'John');

        $result = $filter->toArray();

        self::assertSame(
            [
                [
                    [
                        'Property' => 'Name',
                        'Operator' => Operator::NOT_EQUALS->value,
                        'Value' => 'John',
                    ],
                ],
            ],
            $result
        );
    }

    public function test_to_array_with_boolean(): void
    {
        $filter = new NotEqualsFilter('Active', false);

        $result = $filter->toArray();

        self::assertSame(
            [
                [
                    [
                        'Property' => 'Active',
                        'Operator' => Operator::NOT_EQUALS->value,
                        'Value' => false,
                    ],
                ],
            ],
            $result
        );
    }
}
