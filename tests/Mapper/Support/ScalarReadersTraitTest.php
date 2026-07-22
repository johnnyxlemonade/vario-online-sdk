<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Mapper\Support;

use Lemonade\Vario\Mapper\Support\ScalarReadersTrait;
use PHPUnit\Framework\TestCase;

final class ScalarReadersTraitTest extends TestCase
{
    private ?ScalarReadersTraitTester $t = null;

    protected function setUp(): void
    {
        $this->t = new ScalarReadersTraitTester();
    }

    public function test_string_or_null(): void
    {
        self::assertSame('abc', $this->getTester()->stringOrNull('abc'));
        self::assertSame('123', $this->getTester()->stringOrNull(123));
        self::assertNull($this->getTester()->stringOrNull(null));
        self::assertNull($this->getTester()->stringOrNull(''));
        self::assertNull($this->getTester()->stringOrNull([]));
    }

    public function test_int_or_null(): void
    {
        self::assertSame(5, $this->getTester()->intOrNull(5));
        self::assertSame(5, $this->getTester()->intOrNull('5'));
        self::assertNull($this->getTester()->intOrNull('abc'));
    }

    public function test_float_or_null(): void
    {
        self::assertSame(5.0, $this->getTester()->floatOrNull(5));
        self::assertSame(5.2, $this->getTester()->floatOrNull('5.2'));
        self::assertNull($this->getTester()->floatOrNull('abc'));
    }

    public function test_nullable_trim(): void
    {
        self::assertNull($this->getTester()->nullableTrim(null));
        self::assertNull($this->getTester()->nullableTrim('   '));
        self::assertSame('abc', $this->getTester()->nullableTrim('  abc  '));
    }

    private function getTester(): ScalarReadersTraitTester
    {
        self::assertNotNull($this->t);

        return $this->t;
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
