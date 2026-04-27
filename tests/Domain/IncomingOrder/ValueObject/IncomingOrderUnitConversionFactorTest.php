<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\ValueObject;

use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderUnitOfMeasureScheme;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderUnitConversionFactor;
use PHPUnit\Framework\TestCase;

final class IncomingOrderUnitConversionFactorTest extends TestCase
{
    public function test_it_exposes_all_values(): void
    {
        $factor = new IncomingOrderUnitConversionFactor(
            value: 2.5,
            unitCode: 'm2',
            scheme: IncomingOrderUnitOfMeasureScheme::SI,
        );

        self::assertSame(2.5, $factor->getValue());
        self::assertSame('m2', $factor->getUnitCode());
        self::assertSame(
            IncomingOrderUnitOfMeasureScheme::SI,
            $factor->getScheme()
        );
    }

    public function test_it_uses_unknown_scheme_by_default(): void
    {
        $factor = new IncomingOrderUnitConversionFactor(
            value: 1.0,
            unitCode: 'Ks',
        );

        self::assertSame(1.0, $factor->getValue());
        self::assertSame('Ks', $factor->getUnitCode());
        self::assertSame(
            IncomingOrderUnitOfMeasureScheme::Unknown,
            $factor->getScheme()
        );
    }
}
