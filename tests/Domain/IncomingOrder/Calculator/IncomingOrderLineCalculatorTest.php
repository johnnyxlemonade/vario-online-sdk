<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Calculator;

use InvalidArgumentException;
use Lemonade\Vario\Domain\IncomingOrder\Calculator\IncomingOrderLineCalculator;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPriceMode;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTaxCalculationMethod;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTaxScheme;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderUnitOfMeasureScheme;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderCalculatedLineInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineItemInput;
use PHPUnit\Framework\TestCase;

final class IncomingOrderLineCalculatorTest extends TestCase
{
    public function test_calculate_builds_line_from_tax_exclusive_price(): void
    {
        $calculator = new IncomingOrderLineCalculator();

        $input = new IncomingOrderCalculatedLineInput(
            uuid: 'line-1',
            lineItem: new IncomingOrderLineItemInput(
                catalogueItemIdentification: 'SKU-001',
            ),
            quantity: 2.0,
            unitCode: 'Ks',
            unitPrice: 100.0,
            vatRate: 21.0,
            priceMode: IncomingOrderPriceMode::WithoutVat,
            unitScheme: IncomingOrderUnitOfMeasureScheme::Unknown,
            id: 'ROW-1',
            note: 'row note',
            taxCalculationMethod: IncomingOrderTaxCalculationMethod::Add,
            taxScheme: IncomingOrderTaxScheme::Vat,
            taxSchemeExtensionCode: 'LOCAL-RC',
        );

        $line = $calculator->calculate($input);

        self::assertSame('line-1', $line->getUuid());
        self::assertSame('ROW-1', $line->getId());
        self::assertSame('row note', $line->getNote());

        self::assertSame(200.0, $line->getLineExtensionAmount());
        self::assertSame(242.0, $line->getLineExtensionAmountTaxInclusive());

        self::assertSame($input->getLineItem(), $line->getLineItem());

        self::assertSame(2.0, $line->getLineQuantity()->getValue());
        self::assertSame('Ks', $line->getLineQuantity()->getUnitCode());
        self::assertSame(IncomingOrderUnitOfMeasureScheme::Unknown, $line->getLineQuantity()->getScheme());

        self::assertSame(IncomingOrderTaxCalculationMethod::Add, $line->getTaxSubTotal()->getCalculationMethod());
        self::assertSame(IncomingOrderTaxScheme::Vat, $line->getTaxSubTotal()->getScheme());
        self::assertSame(200.0, $line->getTaxSubTotal()->getTaxableAmount());
        self::assertSame(42.0, $line->getTaxSubTotal()->getTaxAmount());
        self::assertSame(21.0, $line->getTaxSubTotal()->getTaxPercentage());
        self::assertSame('LOCAL-RC', $line->getTaxSubTotal()->getTaxSchemeExtensionCode());
    }

    public function test_calculate_builds_line_from_tax_inclusive_price(): void
    {
        $calculator = new IncomingOrderLineCalculator();

        $input = new IncomingOrderCalculatedLineInput(
            uuid: 'line-2',
            lineItem: new IncomingOrderLineItemInput(
                catalogueItemIdentification: 'SKU-002',
            ),
            quantity: 2.0,
            unitCode: 'Ks',
            unitPrice: 121.0,
            vatRate: 21.0,
            priceMode: IncomingOrderPriceMode::WithVat,
        );

        $line = $calculator->calculate($input);

        self::assertSame(200.0, $line->getLineExtensionAmount());
        self::assertSame(242.0, $line->getLineExtensionAmountTaxInclusive());
        self::assertSame(42.0, $line->getTaxSubTotal()->getTaxAmount());
    }

    public function test_calculate_respects_custom_rounding_scale(): void
    {
        $calculator = new IncomingOrderLineCalculator(scale: 3);

        $input = new IncomingOrderCalculatedLineInput(
            uuid: 'line-3',
            lineItem: new IncomingOrderLineItemInput(
                catalogueItemIdentification: 'SKU-003',
            ),
            quantity: 1.0,
            unitCode: 'Ks',
            unitPrice: 100.555,
            vatRate: 21.0,
            priceMode: IncomingOrderPriceMode::WithoutVat,
        );

        $line = $calculator->calculate($input);

        self::assertSame(100.555, $line->getLineExtensionAmount());
        self::assertSame(21.117, $line->getTaxSubTotal()->getTaxAmount());
        self::assertSame(121.672, $line->getLineExtensionAmountTaxInclusive());
    }

    public function test_calculate_throws_when_quantity_is_zero_or_negative(): void
    {
        $calculator = new IncomingOrderLineCalculator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('IncomingOrder line quantity must be greater than zero.');

        $calculator->calculate($this->createInput(
            quantity: 0.0,
            unitPrice: 100.0,
            vatRate: 21.0,
        ));
    }

    public function test_calculate_throws_when_unit_price_is_negative(): void
    {
        $calculator = new IncomingOrderLineCalculator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('IncomingOrder unit price must not be negative.');

        $calculator->calculate($this->createInput(
            quantity: 1.0,
            unitPrice: -1.0,
            vatRate: 21.0,
        ));
    }

    public function test_calculate_throws_when_vat_rate_is_negative(): void
    {
        $calculator = new IncomingOrderLineCalculator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('IncomingOrder VAT rate must not be negative.');

        $calculator->calculate($this->createInput(
            quantity: 1.0,
            unitPrice: 100.0,
            vatRate: -21.0,
        ));
    }

    private function createInput(
        float $quantity,
        float $unitPrice,
        float $vatRate,
    ): IncomingOrderCalculatedLineInput {
        return new IncomingOrderCalculatedLineInput(
            uuid: 'line-test',
            lineItem: new IncomingOrderLineItemInput(
                catalogueItemIdentification: 'SKU-TEST',
            ),
            quantity: $quantity,
            unitCode: 'Ks',
            unitPrice: $unitPrice,
            vatRate: $vatRate,
        );
    }
}
