<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\OutgoingQuotation\Write;

use InvalidArgumentException;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationLineInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationLineItemInput;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxCalculationMethod;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxScheme;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentQuantity;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxSubTotal;
use PHPUnit\Framework\TestCase;

final class OutgoingQuotationLineInputTest extends TestCase
{
    public function test_with_methods_preserve_line_allowance_amount(): void
    {
        $line = new OutgoingQuotationLineInput(
            uuid: 'line-uuid-1',
            lineExtensionAmount: 100.0,
            lineExtensionAmountTaxInclusive: 121.0,
            lineItem: new OutgoingQuotationLineItemInput(
                catalogueItemIdentification: 'CAT-001',
            ),
            lineQuantity: new DocumentQuantity(
                value: 1.0,
                unitCode: 'Ks',
                scheme: DocumentUnitOfMeasureScheme::Unknown,
            ),
            taxSubTotal: new DocumentTaxSubTotal(
                calculationMethod: DocumentTaxCalculationMethod::Add,
                scheme: DocumentTaxScheme::Vat,
                taxableAmount: 100.0,
                taxAmount: 21.0,
                taxPercentage: 21.0,
                taxSchemeExtensionCode: null,
            ),
            lineAllowanceAmount: 2400.0,
            id: 'ROW-1',
            note: 'Original note',
        );

        self::assertSame(2400.0, $line->withUuid('line-uuid-2')->getLineAllowanceAmount());
        self::assertSame(2400.0, $line->withLineExtensionAmount(200.0)->getLineAllowanceAmount());
        self::assertSame(2400.0, $line->withLineExtensionAmountTaxInclusive(242.0)->getLineAllowanceAmount());
        self::assertSame(2400.0, $line->withLineItem(new OutgoingQuotationLineItemInput())->getLineAllowanceAmount());
        self::assertSame(2400.0, $line->withLineQuantity(new DocumentQuantity(
            value: 2.0,
            unitCode: 'bal',
            scheme: DocumentUnitOfMeasureScheme::SI,
        ))->getLineAllowanceAmount());
        self::assertSame(2400.0, $line->withTaxSubTotal(new DocumentTaxSubTotal(
            calculationMethod: DocumentTaxCalculationMethod::Total,
            scheme: DocumentTaxScheme::Vat,
            taxableAmount: 200.0,
            taxAmount: 42.0,
            taxPercentage: 21.0,
            taxSchemeExtensionCode: null,
        ))->getLineAllowanceAmount());
        self::assertSame(2400.0, $line->withId('ROW-2')->getLineAllowanceAmount());
        self::assertSame(2400.0, $line->withNote('Changed note')->getLineAllowanceAmount());
    }

    public function test_it_rejects_negative_line_allowance_amount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Line allowance amount must not be negative.');

        new OutgoingQuotationLineInput(
            uuid: 'line-uuid-1',
            lineExtensionAmount: 100.0,
            lineExtensionAmountTaxInclusive: 121.0,
            lineItem: new OutgoingQuotationLineItemInput(),
            lineQuantity: new DocumentQuantity(
                value: 1.0,
                unitCode: 'Ks',
                scheme: DocumentUnitOfMeasureScheme::Unknown,
            ),
            taxSubTotal: new DocumentTaxSubTotal(
                calculationMethod: DocumentTaxCalculationMethod::Add,
                scheme: DocumentTaxScheme::Vat,
                taxableAmount: 100.0,
                taxAmount: 21.0,
                taxPercentage: 21.0,
                taxSchemeExtensionCode: null,
            ),
            lineAllowanceAmount: -1.0,
        );
    }
}
