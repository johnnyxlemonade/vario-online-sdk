<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Shared\Document\Write;

use InvalidArgumentException;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentPriceMode;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLinePriceInput;
use PHPUnit\Framework\TestCase;

final class DocumentCalculatedLinePriceInputTest extends TestCase
{
    public function test_it_rejects_negative_line_allowance_amount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Line allowance amount must not be negative.');

        new DocumentCalculatedLinePriceInput(
            unitPrice: 100.0,
            vatRate: 21.0,
            lineAllowanceAmount: -1.0,
        );
    }

    public function test_null_allowance_is_valid(): void
    {
        $price = new DocumentCalculatedLinePriceInput(
            unitPrice: 100.0,
            vatRate: 21.0,
            lineAllowanceAmount: null,
        );

        self::assertNull($price->getLineAllowanceAmount());
    }

    public function test_zero_allowance_is_valid(): void
    {
        $price = new DocumentCalculatedLinePriceInput(
            unitPrice: 100.0,
            vatRate: 21.0,
            lineAllowanceAmount: 0.0,
        );

        self::assertSame(0.0, $price->getLineAllowanceAmount());
    }

    public function test_it_exposes_unit_price_vat_rate_price_mode_and_allowance(): void
    {
        $price = new DocumentCalculatedLinePriceInput(
            unitPrice: 1500.0,
            vatRate: 21.0,
            priceMode: DocumentPriceMode::WithoutVat,
            lineAllowanceAmount: 300.0,
        );

        self::assertSame(1500.0, $price->getUnitPrice());
        self::assertSame(21.0, $price->getVatRate());
        self::assertSame(DocumentPriceMode::WithoutVat, $price->getPriceMode());
        self::assertSame(300.0, $price->getLineAllowanceAmount());
    }
}
