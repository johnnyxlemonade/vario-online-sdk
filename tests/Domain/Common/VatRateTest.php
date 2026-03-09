<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Common;

use Lemonade\Vario\Domain\Common\VatRate;
use PHPUnit\Framework\TestCase;

final class VatRateTest extends TestCase
{
    public function testPercentages(): void
    {
        self::assertSame(21.0, VatRate::STANDARD->percentage());
        self::assertSame(12.0, VatRate::REDUCED->percentage());
        self::assertSame(10.0, VatRate::SECOND_REDUCED->percentage());
    }

    public function testTryFromNullable(): void
    {
        self::assertSame(VatRate::STANDARD, VatRate::tryFromNullable('Základní'));
        self::assertSame(VatRate::REDUCED, VatRate::tryFromNullable('Snížená'));
        self::assertSame(VatRate::SECOND_REDUCED, VatRate::tryFromNullable('Druhá snížená'));
        self::assertNull(VatRate::tryFromNullable(null));
    }

    public function testTryFromNullableInvalidValue(): void
    {
        self::assertNull(VatRate::tryFromNullable('invalid'));
    }
}
