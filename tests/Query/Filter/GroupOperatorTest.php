<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Query\Filter;

use Lemonade\Vario\Query\Filter\GroupOperator;
use PHPUnit\Framework\TestCase;

final class GroupOperatorTest extends TestCase
{
    public function test_enum_values(): void
    {
        self::assertSame('AND', GroupOperator::AND->value);
        self::assertSame('OR', GroupOperator::OR->value);
    }

    public function test_enum_cases(): void
    {
        $cases = GroupOperator::cases();

        self::assertCount(2, $cases);
        self::assertSame(GroupOperator::AND, $cases[0]);
        self::assertSame(GroupOperator::OR, $cases[1]);
    }
}
