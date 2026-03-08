<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Query\Filter;

use Lemonade\Vario\Query\Filter\IsNullFilter;
use Lemonade\Vario\Query\Filter\Operator;
use PHPUnit\Framework\TestCase;

final class IsNullFilterTest extends TestCase
{
    public function test_to_array(): void
    {
        $filter = new IsNullFilter('DeletedAt');

        $result = $filter->toArray();

        self::assertSame(
            [
                [
                    [
                        'Property' => 'DeletedAt',
                        'Operator' => Operator::EQUALS->value,
                        'Value' => null,
                    ],
                ],
            ],
            $result
        );
    }
}
