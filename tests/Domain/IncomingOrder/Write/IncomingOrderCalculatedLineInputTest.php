<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Write;

use InvalidArgumentException;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineItemInput;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentPriceMode;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxCalculationMethod;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxScheme;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineInput;
use PHPUnit\Framework\TestCase;

final class IncomingOrderCalculatedLineInputTest extends TestCase
{
    public function test_it_exposes_all_values(): void
    {
        $lineItem = new IncomingOrderLineItemInput(
            catalogueItemIdentification: 'CAT-001',
            sellersItemIdentification: 'SELL-001',
        );

        $input = new DocumentCalculatedLineInput(
            uuid: 'line-calc-uuid-1',
            lineItem: $lineItem,
            quantity: 5.0,
            unitCode: 'm2',
            unitPrice: 123.45,
            vatRate: 21.0,
            priceMode: DocumentPriceMode::WithVat,
            lineAllowanceAmount: 2400.0,
            unitScheme: DocumentUnitOfMeasureScheme::SI,
            id: 'line-id-1',
            note: 'line calculation',
            taxCalculationMethod: DocumentTaxCalculationMethod::Total,
            taxScheme: DocumentTaxScheme::Vat,
            taxSchemeExtensionCode: 'LOCAL-RC',
        );

        self::assertSame('line-calc-uuid-1', $input->getUuid());
        self::assertSame($lineItem, $input->getLineItem());
        self::assertSame(5.0, $input->getQuantity());
        self::assertSame('m2', $input->getUnitCode());
        self::assertSame(123.45, $input->getUnitPrice());
        self::assertSame(21.0, $input->getVatRate());
        self::assertSame(DocumentPriceMode::WithVat, $input->getPriceMode());
        self::assertSame(2400.0, $input->getLineAllowanceAmount());
        self::assertSame(DocumentUnitOfMeasureScheme::SI, $input->getUnitScheme());
        self::assertSame('line-id-1', $input->getId());
        self::assertSame('line calculation', $input->getNote());
        self::assertSame(DocumentTaxCalculationMethod::Total, $input->getTaxCalculationMethod());
        self::assertSame(DocumentTaxScheme::Vat, $input->getTaxScheme());
        self::assertSame('LOCAL-RC', $input->getTaxSchemeExtensionCode());
    }

    public function test_it_uses_expected_defaults(): void
    {
        $lineItem = new IncomingOrderLineItemInput(
            catalogueItemIdentification: 'CAT-001',
        );

        $input = new DocumentCalculatedLineInput(
            uuid: 'line-calc-uuid-2',
            lineItem: $lineItem,
            quantity: 1.0,
            unitCode: 'Ks',
            unitPrice: 100.0,
            vatRate: 21.0,
        );

        self::assertSame('line-calc-uuid-2', $input->getUuid());
        self::assertSame($lineItem, $input->getLineItem());
        self::assertSame(1.0, $input->getQuantity());
        self::assertSame('Ks', $input->getUnitCode());
        self::assertSame(100.0, $input->getUnitPrice());
        self::assertSame(21.0, $input->getVatRate());
        self::assertSame(DocumentPriceMode::WithoutVat, $input->getPriceMode());
        self::assertNull($input->getLineAllowanceAmount());
        self::assertSame(DocumentUnitOfMeasureScheme::Unknown, $input->getUnitScheme());
        self::assertNull($input->getId());
        self::assertNull($input->getNote());
        self::assertSame(DocumentTaxCalculationMethod::Add, $input->getTaxCalculationMethod());
        self::assertSame(DocumentTaxScheme::Vat, $input->getTaxScheme());
        self::assertNull($input->getTaxSchemeExtensionCode());
    }

    public function test_it_rejects_negative_line_allowance_amount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Line allowance amount must not be negative.');

        new DocumentCalculatedLineInput(
            uuid: 'line-calc-uuid-3',
            lineItem: new IncomingOrderLineItemInput(),
            quantity: 1.0,
            unitCode: 'Ks',
            unitPrice: 100.0,
            vatRate: 21.0,
            lineAllowanceAmount: -1.0,
        );
    }
}
