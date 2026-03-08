<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Mapper\Support;

use Lemonade\Vario\Mapper\Support\ScalarReadersTrait;
use PHPUnit\Framework\TestCase;

final class ScalarReadersTraitTest extends TestCase
{
    private ScalarReadersTraitTester $t;

    protected function setUp(): void
    {
        $this->t = new ScalarReadersTraitTester();
    }

    public function test_string_or_null(): void
    {
        self::assertSame('abc', $this->t->stringOrNull('abc'));
        self::assertSame('123', $this->t->stringOrNull(123));
        self::assertNull($this->t->stringOrNull(null));
        self::assertNull($this->t->stringOrNull(''));
        self::assertNull($this->t->stringOrNull([]));
    }

    public function test_int_or_null(): void
    {
        self::assertSame(5, $this->t->intOrNull(5));
        self::assertSame(5, $this->t->intOrNull('5'));
        self::assertNull($this->t->intOrNull('abc'));
    }

    public function test_float_or_null(): void
    {
        self::assertSame(5.0, $this->t->floatOrNull(5));
        self::assertSame(5.2, $this->t->floatOrNull('5.2'));
        self::assertNull($this->t->floatOrNull('abc'));
    }

    public function test_nullable_trim(): void
    {
        self::assertNull($this->t->nullableTrim(null));
        self::assertNull($this->t->nullableTrim('   '));
        self::assertSame('abc', $this->t->nullableTrim('  abc  '));
    }
}

final class ScalarReadersTraitTester
{
    use ScalarReadersTrait {
        stringOrNull as public;
        string as public;
        intOrNull as public;
        floatOrNull as public;
        nullableTrim as public;
    }
}
