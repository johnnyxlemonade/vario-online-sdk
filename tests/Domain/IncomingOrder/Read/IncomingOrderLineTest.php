<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Read;

use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxCalculationMethod;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxScheme;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentDescription;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderLine;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderLineItem;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderUnitPrice;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentQuantity;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxSubTotal;
use PHPUnit\Framework\TestCase;

final class IncomingOrderLineTest extends TestCase
{
    public function test_it_exposes_all_values(): void
    {
        $lineItem = new IncomingOrderLineItem(
            catalogueItemIdentification: 'SKU-001',
            sellersItemIdentification: 'SELL-001',
            descriptions: [
                new DocumentDescription('Test item', 'cs'),
            ],
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

        $unitPrice = new IncomingOrderUnitPrice(
            amount: 50.0,
            amountTaxInclusive: 60.5,
            quantity: new DocumentQuantity(
                value: 1.0,
                unitCode: 'Ks',
                scheme: DocumentUnitOfMeasureScheme::Unknown,
            ),
        );

        $line = new IncomingOrderLine(
            uuid: 'line-uuid-1',
            id: 'line-id-1',
            lineExtensionAmount: 100.0,
            lineExtensionAmountTaxInclusive: 121.0,
            note: 'PoznÄ‚Ë‡mka k Äąâ„˘Ä‚Ë‡dku',
            lineItem: $lineItem,
            lineQuantity: $quantity,
            taxSubTotal: $taxSubTotal,
            unitPrice: $unitPrice,
            extra: [
                'CustomField' => 'custom-value',
            ],
        );

        self::assertSame('line-uuid-1', $line->getUuid());
        self::assertSame('line-id-1', $line->getId());
        self::assertSame(100.0, $line->getLineExtensionAmount());
        self::assertSame(121.0, $line->getLineExtensionAmountTaxInclusive());
        self::assertSame('PoznÄ‚Ë‡mka k Äąâ„˘Ä‚Ë‡dku', $line->getNote());
        self::assertSame($lineItem, $line->getLineItem());
        self::assertSame($quantity, $line->getLineQuantity());
        self::assertSame($taxSubTotal, $line->getTaxSubTotal());
        self::assertSame($unitPrice, $line->getUnitPrice());

        self::assertTrue($line->hasLineItem());
        self::assertTrue($line->hasQuantity());
        self::assertTrue($line->hasTaxSubTotal());
        self::assertTrue($line->hasUnitPrice());

        self::assertSame([
            'CustomField' => 'custom-value',
        ], $line->getExtra());
    }

    public function test_it_supports_empty_state(): void
    {
        $line = new IncomingOrderLine();

        self::assertNull($line->getUuid());
        self::assertNull($line->getId());
        self::assertNull($line->getLineExtensionAmount());
        self::assertNull($line->getLineExtensionAmountTaxInclusive());
        self::assertNull($line->getNote());
        self::assertNull($line->getLineItem());
        self::assertNull($line->getLineQuantity());
        self::assertNull($line->getTaxSubTotal());
        self::assertNull($line->getUnitPrice());

        self::assertFalse($line->hasLineItem());
        self::assertFalse($line->hasQuantity());
        self::assertFalse($line->hasTaxSubTotal());
        self::assertFalse($line->hasUnitPrice());

        self::assertSame([], $line->getExtra());
    }
}
