<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Write;

use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineItemInput;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxCalculationMethod;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxScheme;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentQuantity;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxSubTotal;
use PHPUnit\Framework\TestCase;

final class IncomingOrderLineInputTest extends TestCase
{
    public function test_it_exposes_constructor_values(): void
    {
        $lineItem = new IncomingOrderLineItemInput(
            catalogueItemIdentification: 'CAT-001',
        );

        $quantity = new DocumentQuantity(
            value: 2.0,
            unitCode: 'Ks',
            scheme: DocumentUnitOfMeasureScheme::Unknown,
        );

        $taxSubTotal = new DocumentTaxSubTotal(
            calculationMethod: DocumentTaxCalculationMethod::Add,
            scheme: DocumentTaxScheme::Vat,
            taxableAmount: 100.0,
            taxAmount: 21.0,
            taxPercentage: 21.0,
            taxSchemeExtensionCode: null,
        );

        $line = new IncomingOrderLineInput(
            uuid: 'line-uuid-1',
            lineExtensionAmount: 100.0,
            lineExtensionAmountTaxInclusive: 121.0,
            lineItem: $lineItem,
            lineQuantity: $quantity,
            taxSubTotal: $taxSubTotal,
            id: 'line-id-1',
            note: 'poznámka',
        );

        self::assertSame('line-uuid-1', $line->getUuid());
        self::assertSame(100.0, $line->getLineExtensionAmount());
        self::assertSame(121.0, $line->getLineExtensionAmountTaxInclusive());
        self::assertSame($lineItem, $line->getLineItem());
        self::assertSame($quantity, $line->getLineQuantity());
        self::assertSame($taxSubTotal, $line->getTaxSubTotal());
        self::assertSame('line-id-1', $line->getId());
        self::assertSame('poznámka', $line->getNote());
    }

    public function test_with_methods_update_values(): void
    {
        $lineItem1 = new IncomingOrderLineItemInput(
            catalogueItemIdentification: 'CAT-001',
        );

        $lineItem2 = new IncomingOrderLineItemInput(
            catalogueItemIdentification: 'CAT-002',
        );

        $quantity1 = new DocumentQuantity(
            value: 1.0,
            unitCode: 'Ks',
            scheme: DocumentUnitOfMeasureScheme::Unknown,
        );

        $quantity2 = new DocumentQuantity(
            value: 3.0,
            unitCode: 'm2',
            scheme: DocumentUnitOfMeasureScheme::SI,
        );

        $tax1 = new DocumentTaxSubTotal(
            calculationMethod: DocumentTaxCalculationMethod::Add,
            scheme: DocumentTaxScheme::Vat,
            taxableAmount: 100.0,
            taxAmount: 21.0,
            taxPercentage: 21.0,
            taxSchemeExtensionCode: null,
        );

        $tax2 = new DocumentTaxSubTotal(
            calculationMethod: DocumentTaxCalculationMethod::Total,
            scheme: DocumentTaxScheme::Vat,
            taxableAmount: 300.0,
            taxAmount: 63.0,
            taxPercentage: 21.0,
            taxSchemeExtensionCode: null,
        );

        $line = new IncomingOrderLineInput(
            uuid: 'line-uuid-1',
            lineExtensionAmount: 100.0,
            lineExtensionAmountTaxInclusive: 121.0,
            lineItem: $lineItem1,
            lineQuantity: $quantity1,
            taxSubTotal: $tax1,
        );

        $result = $line
            ->withUuid('line-uuid-2')
            ->withLineExtensionAmount(300.0)
            ->withLineExtensionAmountTaxInclusive(363.0)
            ->withLineItem($lineItem2)
            ->withLineQuantity($quantity2)
            ->withTaxSubTotal($tax2)
            ->withId('line-id-2')
            ->withNote('nová poznámka');

        self::assertSame($line, $result);

        self::assertSame('line-uuid-2', $line->getUuid());
        self::assertSame(300.0, $line->getLineExtensionAmount());
        self::assertSame(363.0, $line->getLineExtensionAmountTaxInclusive());
        self::assertSame($lineItem2, $line->getLineItem());
        self::assertSame($quantity2, $line->getLineQuantity());
        self::assertSame($tax2, $line->getTaxSubTotal());
        self::assertSame('line-id-2', $line->getId());
        self::assertSame('nová poznámka', $line->getNote());
    }

    public function test_it_supports_nullable_fields(): void
    {
        $line = new IncomingOrderLineInput(
            uuid: 'line-uuid-1',
            lineExtensionAmount: 100.0,
            lineExtensionAmountTaxInclusive: 121.0,
            lineItem: new IncomingOrderLineItemInput(),
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
        );

        self::assertNull($line->getId());
        self::assertNull($line->getNote());

        $line
            ->withId(null)
            ->withNote(null);

        self::assertNull($line->getId());
        self::assertNull($line->getNote());
    }
}
