<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Write;

use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPriceMode;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTaxCalculationMethod;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTaxScheme;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderUnitOfMeasureScheme;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderCalculatedLineInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineItemInput;
use PHPUnit\Framework\TestCase;

final class IncomingOrderCalculatedLineInputTest extends TestCase
{
    public function test_it_exposes_all_values(): void
    {
        $lineItem = new IncomingOrderLineItemInput(
            catalogueItemIdentification: 'CAT-001',
            sellersItemIdentification: 'SELL-001',
        );

        $input = new IncomingOrderCalculatedLineInput(
            uuid: 'line-calc-uuid-1',
            lineItem: $lineItem,
            quantity: 5.0,
            unitCode: 'm2',
            unitPrice: 123.45,
            vatRate: 21.0,
            priceMode: IncomingOrderPriceMode::WithVat,
            unitScheme: IncomingOrderUnitOfMeasureScheme::SI,
            id: 'line-id-1',
            note: 'výpočet řádku',
            taxCalculationMethod: IncomingOrderTaxCalculationMethod::Total,
            taxScheme: IncomingOrderTaxScheme::Vat,
            taxSchemeExtensionCode: 'LOCAL-RC',
        );

        self::assertSame('line-calc-uuid-1', $input->getUuid());
        self::assertSame($lineItem, $input->getLineItem());
        self::assertSame(5.0, $input->getQuantity());
        self::assertSame('m2', $input->getUnitCode());
        self::assertSame(123.45, $input->getUnitPrice());
        self::assertSame(21.0, $input->getVatRate());
        self::assertSame(IncomingOrderPriceMode::WithVat, $input->getPriceMode());
        self::assertSame(IncomingOrderUnitOfMeasureScheme::SI, $input->getUnitScheme());
        self::assertSame('line-id-1', $input->getId());
        self::assertSame('výpočet řádku', $input->getNote());
        self::assertSame(IncomingOrderTaxCalculationMethod::Total, $input->getTaxCalculationMethod());
        self::assertSame(IncomingOrderTaxScheme::Vat, $input->getTaxScheme());
        self::assertSame('LOCAL-RC', $input->getTaxSchemeExtensionCode());
    }

    public function test_it_uses_expected_defaults(): void
    {
        $lineItem = new IncomingOrderLineItemInput(
            catalogueItemIdentification: 'CAT-001',
        );

        $input = new IncomingOrderCalculatedLineInput(
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
        self::assertSame(IncomingOrderPriceMode::WithoutVat, $input->getPriceMode());
        self::assertSame(IncomingOrderUnitOfMeasureScheme::Unknown, $input->getUnitScheme());
        self::assertNull($input->getId());
        self::assertNull($input->getNote());
        self::assertSame(IncomingOrderTaxCalculationMethod::Add, $input->getTaxCalculationMethod());
        self::assertSame(IncomingOrderTaxScheme::Vat, $input->getTaxScheme());
        self::assertNull($input->getTaxSchemeExtensionCode());
    }
}
