<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Query\Filter;

use Lemonade\Vario\Query\Filter\Operator;
use Lemonade\Vario\Query\Filter\StartsWithFilter;
use PHPUnit\Framework\TestCase;

final class StartsWithFilterTest extends TestCase
{
    public function test_to_array(): void
    {
        $filter = new StartsWithFilter('Name', 'Jo');

        $result = $filter->toArray();

        self::assertSame(
            [
                [
                    [
                        'Property' => 'Name',
                        'Operator' => Operator::STARTS_WITH->value,
                        'Value' => 'Jo',
                    ],
                ],
            ],
            $result
        );
    }
}
