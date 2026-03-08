<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Query\Filter;

use Lemonade\Vario\Query\Filter\EndsWithFilter;
use Lemonade\Vario\Query\Filter\Operator;
use PHPUnit\Framework\TestCase;

final class EndsWithFilterTest extends TestCase
{
    public function test_to_array(): void
    {
        $filter = new EndsWithFilter('Name', 'son');

        $result = $filter->toArray();

        self::assertSame(
            [
                [
                    [
                        'Property' => 'Name',
                        'Operator' => Operator::ENDS_WITH->value,
                        'Value' => 'son',
                    ],
                ],
            ],
            $result
        );
    }
}
