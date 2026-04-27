<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Calculator;

use Lemonade\Vario\Domain\IncomingOrder\Calculator\IncomingOrderTotalsCalculator;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTaxCalculationMethod;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTaxScheme;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderUnitOfMeasureScheme;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderQuantity;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderTaxSubTotal;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineItemInput;
use PHPUnit\Framework\TestCase;

final class IncomingOrderTotalsCalculatorTest extends TestCase
{
    public function test_calculate_aggregates_document_totals_and_merges_same_tax_group(): void
    {
        $calculator = new IncomingOrderTotalsCalculator();

        $result = $calculator->calculate([
            $this->createLine(
                uuid: 'line-1',
                lineExtensionAmount: 100.0,
                lineExtensionAmountTaxInclusive: 121.0,
                taxSubTotal: new IncomingOrderTaxSubTotal(
                    calculationMethod: IncomingOrderTaxCalculationMethod::Add,
                    scheme: IncomingOrderTaxScheme::Vat,
                    taxableAmount: 100.0,
                    taxAmount: 21.0,
                    taxPercentage: 21.0,
                    taxSchemeExtensionCode: null,
                ),
            ),
            $this->createLine(
                uuid: 'line-2',
                lineExtensionAmount: 200.0,
                lineExtensionAmountTaxInclusive: 242.0,
                taxSubTotal: new IncomingOrderTaxSubTotal(
                    calculationMethod: IncomingOrderTaxCalculationMethod::Add,
                    scheme: IncomingOrderTaxScheme::Vat,
                    taxableAmount: 200.0,
                    taxAmount: 42.0,
                    taxPercentage: 21.0,
                    taxSchemeExtensionCode: null,
                ),
            ),
        ]);

        $monetaryTotal = $result['monetaryTotal'];
        $taxTotal = $result['taxTotal'];

        self::assertSame(363.0, $monetaryTotal->getPayableAmount());
        self::assertSame(0.0, $monetaryTotal->getPayableRoundingAmount());
        self::assertSame(300.0, $monetaryTotal->getTaxExclusiveAmount());
        self::assertSame(363.0, $monetaryTotal->getTaxInclusiveAmount());

        self::assertSame(63.0, $taxTotal->getTaxAmount());
        self::assertCount(1, $taxTotal->getTaxSubTotals());

        $taxSubTotal = $taxTotal->getTaxSubTotals()[0];

        self::assertSame(IncomingOrderTaxCalculationMethod::Add, $taxSubTotal->getCalculationMethod());
        self::assertSame(IncomingOrderTaxScheme::Vat, $taxSubTotal->getScheme());
        self::assertSame(300.0, $taxSubTotal->getTaxableAmount());
        self::assertSame(63.0, $taxSubTotal->getTaxAmount());
        self::assertSame(21.0, $taxSubTotal->getTaxPercentage());
        self::assertNull($taxSubTotal->getTaxSchemeExtensionCode());
    }

    public function test_calculate_keeps_separate_tax_groups_when_group_key_differs(): void
    {
        $calculator = new IncomingOrderTotalsCalculator();

        $result = $calculator->calculate([
            $this->createLine(
                uuid: 'line-1',
                lineExtensionAmount: 100.0,
                lineExtensionAmountTaxInclusive: 121.0,
                taxSubTotal: new IncomingOrderTaxSubTotal(
                    calculationMethod: IncomingOrderTaxCalculationMethod::Add,
                    scheme: IncomingOrderTaxScheme::Vat,
                    taxableAmount: 100.0,
                    taxAmount: 21.0,
                    taxPercentage: 21.0,
                    taxSchemeExtensionCode: null,
                ),
            ),
            $this->createLine(
                uuid: 'line-2',
                lineExtensionAmount: 50.0,
                lineExtensionAmountTaxInclusive: 60.5,
                taxSubTotal: new IncomingOrderTaxSubTotal(
                    calculationMethod: IncomingOrderTaxCalculationMethod::Add,
                    scheme: IncomingOrderTaxScheme::Vat,
                    taxableAmount: 50.0,
                    taxAmount: 10.5,
                    taxPercentage: 21.0,
                    taxSchemeExtensionCode: 'LOCAL-RC',
                ),
            ),
            $this->createLine(
                uuid: 'line-3',
                lineExtensionAmount: 100.0,
                lineExtensionAmountTaxInclusive: 112.0,
                taxSubTotal: new IncomingOrderTaxSubTotal(
                    calculationMethod: IncomingOrderTaxCalculationMethod::Add,
                    scheme: IncomingOrderTaxScheme::Vat,
                    taxableAmount: 100.0,
                    taxAmount: 12.0,
                    taxPercentage: 12.0,
                    taxSchemeExtensionCode: null,
                ),
            ),
            $this->createLine(
                uuid: 'line-4',
                lineExtensionAmount: 10.0,
                lineExtensionAmountTaxInclusive: 12.1,
                taxSubTotal: new IncomingOrderTaxSubTotal(
                    calculationMethod: IncomingOrderTaxCalculationMethod::Total,
                    scheme: IncomingOrderTaxScheme::Vat,
                    taxableAmount: 10.0,
                    taxAmount: 2.1,
                    taxPercentage: 21.0,
                    taxSchemeExtensionCode: null,
                ),
            ),
        ]);

        $monetaryTotal = $result['monetaryTotal'];
        $taxTotal = $result['taxTotal'];

        self::assertSame(305.6, $monetaryTotal->getPayableAmount());
        self::assertSame(260.0, $monetaryTotal->getTaxExclusiveAmount());

        self::assertSame(45.6, $taxTotal->getTaxAmount());
        self::assertCount(4, $taxTotal->getTaxSubTotals());

        self::assertSame(100.0, $taxTotal->getTaxSubTotals()[0]->getTaxableAmount());
        self::assertNull($taxTotal->getTaxSubTotals()[0]->getTaxSchemeExtensionCode());

        self::assertSame(50.0, $taxTotal->getTaxSubTotals()[1]->getTaxableAmount());
        self::assertSame('LOCAL-RC', $taxTotal->getTaxSubTotals()[1]->getTaxSchemeExtensionCode());

        self::assertSame(12.0, $taxTotal->getTaxSubTotals()[2]->getTaxPercentage());

        self::assertSame(
            IncomingOrderTaxCalculationMethod::Total,
            $taxTotal->getTaxSubTotals()[3]->getCalculationMethod()
        );
    }

    public function test_calculate_returns_zero_totals_for_empty_input(): void
    {
        $calculator = new IncomingOrderTotalsCalculator();

        $result = $calculator->calculate([]);

        $monetaryTotal = $result['monetaryTotal'];
        $taxTotal = $result['taxTotal'];

        self::assertSame(0.0, $monetaryTotal->getPayableAmount());
        self::assertSame(0.0, $monetaryTotal->getPayableRoundingAmount());
        self::assertSame(0.0, $monetaryTotal->getTaxExclusiveAmount());
        self::assertSame(0.0, $monetaryTotal->getTaxInclusiveAmount());

        self::assertSame(0.0, $taxTotal->getTaxAmount());
        self::assertSame([], $taxTotal->getTaxSubTotals());
    }

    public function test_calculate_respects_custom_rounding_scale(): void
    {
        $calculator = new IncomingOrderTotalsCalculator(scale: 3);

        $result = $calculator->calculate([
            $this->createLine(
                uuid: 'line-1',
                lineExtensionAmount: 100.5554,
                lineExtensionAmountTaxInclusive: 121.6716,
                taxSubTotal: new IncomingOrderTaxSubTotal(
                    calculationMethod: IncomingOrderTaxCalculationMethod::Add,
                    scheme: IncomingOrderTaxScheme::Vat,
                    taxableAmount: 100.5554,
                    taxAmount: 21.1162,
                    taxPercentage: 21.0,
                    taxSchemeExtensionCode: null,
                ),
            ),
        ]);

        $monetaryTotal = $result['monetaryTotal'];
        $taxTotal = $result['taxTotal'];

        self::assertSame(121.672, $monetaryTotal->getPayableAmount());
        self::assertSame(100.555, $monetaryTotal->getTaxExclusiveAmount());
        self::assertSame(21.116, $taxTotal->getTaxAmount());

        self::assertCount(1, $taxTotal->getTaxSubTotals());
        self::assertSame(100.555, $taxTotal->getTaxSubTotals()[0]->getTaxableAmount());
        self::assertSame(21.116, $taxTotal->getTaxSubTotals()[0]->getTaxAmount());
    }

    private function createLine(
        string $uuid,
        float $lineExtensionAmount,
        float $lineExtensionAmountTaxInclusive,
        IncomingOrderTaxSubTotal $taxSubTotal,
    ): IncomingOrderLineInput {
        return new IncomingOrderLineInput(
            uuid: $uuid,
            lineExtensionAmount: $lineExtensionAmount,
            lineExtensionAmountTaxInclusive: $lineExtensionAmountTaxInclusive,
            lineItem: new IncomingOrderLineItemInput(
                catalogueItemIdentification: 'SKU-' . $uuid,
            ),
            lineQuantity: new IncomingOrderQuantity(
                value: 1.0,
                unitCode: 'Ks',
                scheme: IncomingOrderUnitOfMeasureScheme::Unknown,
            ),
            taxSubTotal: $taxSubTotal,
        );
    }
}
