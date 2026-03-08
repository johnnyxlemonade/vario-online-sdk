<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Query\Filter;

use Lemonade\Vario\Query\Filter\NotNullFilter;
use Lemonade\Vario\Query\Filter\Operator;
use PHPUnit\Framework\TestCase;

final class NotNullFilterTest extends TestCase
{
    public function test_to_array(): void
    {
        $filter = new NotNullFilter('DeletedAt');

        $result = $filter->toArray();

        self::assertSame(
            [
                [
                    [
                        'Property' => 'DeletedAt',
                        'Operator' => Operator::NOT_EQUALS->value,
                        'Value' => null,
                    ],
                ],
            ],
            $result
        );
    }
}
