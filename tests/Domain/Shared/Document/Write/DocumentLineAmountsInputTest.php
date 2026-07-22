<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Shared\Document\Write;

use InvalidArgumentException;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentLineAmountsInput;
use PHPUnit\Framework\TestCase;

final class DocumentLineAmountsInputTest extends TestCase
{
    public function test_it_exposes_values(): void
    {
        $amounts = new DocumentLineAmountsInput(
            lineExtensionAmount: 100.0,
            lineExtensionAmountTaxInclusive: 121.0,
            lineAllowanceAmount: 2400.0,
        );

        self::assertSame(100.0, $amounts->getLineExtensionAmount());
        self::assertSame(121.0, $amounts->getLineExtensionAmountTaxInclusive());
        self::assertSame(2400.0, $amounts->getLineAllowanceAmount());
    }

    public function test_it_accepts_null_and_zero_allowance(): void
    {
        self::assertNull((new DocumentLineAmountsInput(
            lineExtensionAmount: 100.0,
            lineExtensionAmountTaxInclusive: 121.0,
        ))->getLineAllowanceAmount());

        self::assertSame(0.0, (new DocumentLineAmountsInput(
            lineExtensionAmount: 100.0,
            lineExtensionAmountTaxInclusive: 121.0,
            lineAllowanceAmount: 0.0,
        ))->getLineAllowanceAmount());
    }

    public function test_it_rejects_negative_allowance(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Line allowance amount must not be negative.');

        new DocumentLineAmountsInput(
            lineExtensionAmount: 100.0,
            lineExtensionAmountTaxInclusive: 121.0,
            lineAllowanceAmount: -1.0,
        );
    }
}
