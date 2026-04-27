<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Read;

use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderNumericAttributeKind;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTextualAttributeKind;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderUnitOfMeasureScheme;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderAdditionalAttribute;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderDescription;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderLineItem;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderNumericAttribute;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderTextualAttribute;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderUnitConversionFactor;
use PHPUnit\Framework\TestCase;

final class IncomingOrderLineItemTest extends TestCase
{
    public function test_it_exposes_all_values(): void
    {
        $description1 = new IncomingOrderDescription('Thermax ECO 1000x610x30 mm', 'cs');
        $description2 = new IncomingOrderDescription('Thermax ECO', 'en');

        $additionalAttribute = new IncomingOrderAdditionalAttribute(
            attributeKind: IncomingOrderTextualAttributeKind::ExtendedID,
            name: 'Varianta',
            value: 'velká díra',
            langId: 'cs',
            unitCode: null,
            scheme: IncomingOrderUnitOfMeasureScheme::Unknown,
        );

        $textualAttribute = new IncomingOrderTextualAttribute(
            attributeKind: IncomingOrderTextualAttributeKind::ExtendedID,
            name: 'Sklad',
            value: 'Mělník',
            langId: 'cs',
        );

        $numericAttribute = new IncomingOrderNumericAttribute(
            attributeKind: IncomingOrderNumericAttributeKind::FreeNumeric,
            name: 'Pocet_polozky_1',
            value: 5.0,
            unitCode: 'Ks',
        );

        $factor1 = new IncomingOrderUnitConversionFactor(
            value: 1.0,
            unitCode: 'Ks',
            scheme: IncomingOrderUnitOfMeasureScheme::Unknown,
        );

        $factor2 = new IncomingOrderUnitConversionFactor(
            value: 2.0,
            unitCode: 'm2',
            scheme: IncomingOrderUnitOfMeasureScheme::SI,
        );

        $item = new IncomingOrderLineItem(
            buyersItemIdentification: 'BUY-001',
            catalogueItemIdentification: 'CAT-001',
            sellersItemIdentification: 'SELL-001',
            standardItemIdentification: 'EAN-001',
            descriptions: [$description1, $description2],
            additionalAttributes: [$additionalAttribute],
            textualAttributes: [$textualAttribute],
            numericAttributes: [$numericAttribute],
            unitConversionFactors: [$factor1, $factor2],
            extra: [
                'CustomField' => 'custom-value',
            ],
        );

        self::assertSame('BUY-001', $item->getBuyersItemIdentification());
        self::assertSame('CAT-001', $item->getCatalogueItemIdentification());
        self::assertSame('SELL-001', $item->getSellersItemIdentification());
        self::assertSame('EAN-001', $item->getStandardItemIdentification());

        self::assertTrue($item->hasAnyProductIdentification());

        self::assertTrue($item->hasDescriptions());
        self::assertSame([$description1, $description2], $item->getDescriptions());
        self::assertSame($description1, $item->getPrimaryDescription());
        self::assertSame('Thermax ECO 1000x610x30 mm', $item->getPrimaryDescriptionText());

        self::assertTrue($item->hasAdditionalAttributes());
        self::assertSame([$additionalAttribute], $item->getAdditionalAttributes());

        self::assertTrue($item->hasTextualAttributes());
        self::assertSame([$textualAttribute], $item->getTextualAttributes());

        self::assertTrue($item->hasNumericAttributes());
        self::assertSame([$numericAttribute], $item->getNumericAttributes());

        self::assertTrue($item->hasUnitConversionFactors());
        self::assertSame([$factor1, $factor2], $item->getUnitConversionFactors());

        self::assertSame([
            'CustomField' => 'custom-value',
        ], $item->getExtra());
    }

    public function test_it_supports_empty_state(): void
    {
        $item = new IncomingOrderLineItem();

        self::assertNull($item->getBuyersItemIdentification());
        self::assertNull($item->getCatalogueItemIdentification());
        self::assertNull($item->getSellersItemIdentification());
        self::assertNull($item->getStandardItemIdentification());

        self::assertFalse($item->hasAnyProductIdentification());

        self::assertFalse($item->hasDescriptions());
        self::assertSame([], $item->getDescriptions());
        self::assertNull($item->getPrimaryDescription());
        self::assertNull($item->getPrimaryDescriptionText());

        self::assertFalse($item->hasAdditionalAttributes());
        self::assertSame([], $item->getAdditionalAttributes());

        self::assertFalse($item->hasTextualAttributes());
        self::assertSame([], $item->getTextualAttributes());

        self::assertFalse($item->hasNumericAttributes());
        self::assertSame([], $item->getNumericAttributes());

        self::assertFalse($item->hasUnitConversionFactors());
        self::assertSame([], $item->getUnitConversionFactors());

        self::assertSame([], $item->getExtra());
    }

    public function test_buyers_item_identification_alone_counts_as_product_identification(): void
    {
        $item = new IncomingOrderLineItem(
            buyersItemIdentification: 'BUY-ONLY',
        );

        self::assertTrue($item->hasAnyProductIdentification());
    }
}
