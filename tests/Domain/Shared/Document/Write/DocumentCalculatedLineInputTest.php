<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Shared\Document\Write;

use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineItemInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationLineItemInput;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentPriceMode;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxCalculationMethod;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxScheme;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineIdentityInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLinePriceInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineQuantityInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineTaxInput;
use PHPUnit\Framework\TestCase;

final class DocumentCalculatedLineInputTest extends TestCase
{
    public function test_it_delegates_getters_to_nested_inputs(): void
    {
        $lineItem = new IncomingOrderLineItemInput(
            catalogueItemIdentification: 'CAT-001',
            sellersItemIdentification: 'SELL-001',
        );

        $input = new DocumentCalculatedLineInput(
            identity: new DocumentCalculatedLineIdentityInput(
                uuid: 'line-calc-uuid-1',
                id: 'line-id-1',
                note: 'line calculation',
            ),
            lineItem: $lineItem,
            quantity: new DocumentCalculatedLineQuantityInput(
                value: 5.0,
                unitCode: 'm2',
                scheme: DocumentUnitOfMeasureScheme::SI,
            ),
            price: new DocumentCalculatedLinePriceInput(
                unitPrice: 123.45,
                vatRate: 21.0,
                priceMode: DocumentPriceMode::WithVat,
                lineAllowanceAmount: 2400.0,
            ),
            tax: new DocumentCalculatedLineTaxInput(
                calculationMethod: DocumentTaxCalculationMethod::Total,
                scheme: DocumentTaxScheme::Vat,
                schemeExtensionCode: 'LOCAL-RC',
            ),
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

    public function test_it_preserves_line_item_template_type_for_outgoing_line_item(): void
    {
        $lineItem = new OutgoingQuotationLineItemInput(
            catalogueItemIdentification: 'SKU-001',
        );

        $input = new DocumentCalculatedLineInput(
            identity: new DocumentCalculatedLineIdentityInput(
                uuid: 'line-calc-uuid-2',
            ),
            lineItem: $lineItem,
            quantity: new DocumentCalculatedLineQuantityInput(
                value: 1.0,
                unitCode: 'Ks',
            ),
            price: new DocumentCalculatedLinePriceInput(
                unitPrice: 100.0,
                vatRate: 21.0,
            ),
        );

        self::assertInstanceOf(OutgoingQuotationLineItemInput::class, $input->getLineItem());
    }
}
