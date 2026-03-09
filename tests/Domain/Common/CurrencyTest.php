<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Common;

use Lemonade\Vario\Domain\Common\Currency;
use PHPUnit\Framework\TestCase;

final class CurrencyTest extends TestCase
{
    public function testTryFromNullable(): void
    {
        self::assertSame(Currency::CZK, Currency::tryFromNullable('CZK'));
        self::assertSame(Currency::EUR, Currency::tryFromNullable('EUR'));
        self::assertSame(Currency::USD, Currency::tryFromNullable('USD'));
    }

    public function testTryFromNullableIsCaseInsensitive(): void
    {
        self::assertSame(Currency::CZK, Currency::tryFromNullable('czk'));
        self::assertSame(Currency::EUR, Currency::tryFromNullable('eur'));
    }

    public function testTryFromNullableReturnsNullForEmpty(): void
    {
        self::assertNull(Currency::tryFromNullable(null));
        self::assertNull(Currency::tryFromNullable(''));
    }

    public function testTryFromNullableInvalidCurrency(): void
    {
        self::assertNull(Currency::tryFromNullable('INVALID'));
    }
}
