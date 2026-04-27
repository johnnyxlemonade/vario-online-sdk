<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Write;

use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTextualAttributeKind;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderUnitOfMeasureScheme;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderDescription;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderUnitConversionFactor;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderAdditionalAttributeInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineItemInput;
use PHPUnit\Framework\TestCase;

final class IncomingOrderLineItemInputTest extends TestCase
{
    public function test_it_exposes_constructor_values(): void
    {
        $item = new IncomingOrderLineItemInput(
            buyersItemIdentification: 'BUY-001',
            catalogueItemIdentification: 'CAT-001',
            sellersItemIdentification: 'SELL-001',
            standardItemIdentification: 'EAN-001',
        );

        self::assertSame('BUY-001', $item->getBuyersItemIdentification());
        self::assertSame('CAT-001', $item->getCatalogueItemIdentification());
        self::assertSame('SELL-001', $item->getSellersItemIdentification());
        self::assertSame('EAN-001', $item->getStandardItemIdentification());
    }

    public function test_with_methods_update_identifications(): void
    {
        $item = new IncomingOrderLineItemInput();

        $result = $item
            ->withBuyersItemIdentification('BUY-002')
            ->withCatalogueItemIdentification('CAT-002')
            ->withSellersItemIdentification('SELL-002')
            ->withStandardItemIdentification('EAN-002');

        self::assertSame($item, $result);
        self::assertSame('BUY-002', $item->getBuyersItemIdentification());
        self::assertSame('CAT-002', $item->getCatalogueItemIdentification());
        self::assertSame('SELL-002', $item->getSellersItemIdentification());
        self::assertSame('EAN-002', $item->getStandardItemIdentification());
    }

    public function test_it_can_add_and_replace_descriptions(): void
    {
        $item = new IncomingOrderLineItemInput();

        $first = new IncomingOrderDescription('První popis', 'cs');
        $second = new IncomingOrderDescription('Second description', 'en');

        self::assertSame($item, $item->addDescription($first));
        self::assertSame([$first], $item->getDescriptions());

        self::assertSame($item, $item->withDescriptions([$first, $second]));
        self::assertSame([$first, $second], $item->getDescriptions());
    }

    public function test_it_can_add_and_replace_additional_attributes(): void
    {
        $item = new IncomingOrderLineItemInput();

        $first = new IncomingOrderAdditionalAttributeInput(
            name: 'Varianta',
            value: 'velká díra',
            attributeKind: IncomingOrderTextualAttributeKind::ExtendedID,
            langId: 'cs',
            unitCode: null,
            scheme: IncomingOrderUnitOfMeasureScheme::Unknown,
        );

        $second = new IncomingOrderAdditionalAttributeInput(
            name: 'Barva',
            value: 'černá',
            attributeKind: IncomingOrderTextualAttributeKind::FreeText,
            langId: 'cs',
            unitCode: null,
            scheme: null,
        );

        self::assertSame($item, $item->addAdditionalAttribute($first));
        self::assertSame([$first], $item->getAdditionalAttributes());

        self::assertSame($item, $item->withAdditionalAttributes([$first, $second]));
        self::assertSame([$first, $second], $item->getAdditionalAttributes());
    }

    public function test_it_can_add_and_replace_unit_conversion_factors(): void
    {
        $item = new IncomingOrderLineItemInput();

        $first = new IncomingOrderUnitConversionFactor(
            value: 1.0,
            unitCode: 'Ks',
            scheme: IncomingOrderUnitOfMeasureScheme::Unknown,
        );

        $second = new IncomingOrderUnitConversionFactor(
            value: 2.0,
            unitCode: 'm2',
            scheme: IncomingOrderUnitOfMeasureScheme::SI,
        );

        self::assertSame($item, $item->addUnitConversionFactor($first));
        self::assertSame([$first], $item->getUnitConversionFactors());

        self::assertSame($item, $item->withUnitConversionFactors([$first, $second]));
        self::assertSame([$first, $second], $item->getUnitConversionFactors());
    }

    public function test_has_any_product_identification_ignores_buyers_identification(): void
    {
        $item = new IncomingOrderLineItemInput();

        self::assertFalse($item->hasAnyProductIdentification());

        $item->withBuyersItemIdentification('BUY-ONLY');
        self::assertFalse($item->hasAnyProductIdentification());

        $item->withCatalogueItemIdentification('CAT-001');
        self::assertTrue($item->hasAnyProductIdentification());

        $item
            ->withCatalogueItemIdentification(null)
            ->withSellersItemIdentification('SELL-001');
        self::assertTrue($item->hasAnyProductIdentification());

        $item
            ->withSellersItemIdentification(null)
            ->withStandardItemIdentification('EAN-001');
        self::assertTrue($item->hasAnyProductIdentification());
    }

    public function test_it_supports_empty_state(): void
    {
        $item = new IncomingOrderLineItemInput();

        self::assertNull($item->getBuyersItemIdentification());
        self::assertNull($item->getCatalogueItemIdentification());
        self::assertNull($item->getSellersItemIdentification());
        self::assertNull($item->getStandardItemIdentification());
        self::assertSame([], $item->getDescriptions());
        self::assertSame([], $item->getAdditionalAttributes());
        self::assertSame([], $item->getUnitConversionFactors());
        self::assertFalse($item->hasAnyProductIdentification());
    }
}
