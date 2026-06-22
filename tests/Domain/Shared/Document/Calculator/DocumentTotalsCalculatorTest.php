<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Shared\Document\Calculator;

use Lemonade\Vario\Domain\Shared\Document\Calculator\DocumentTotalsCalculator;
use Lemonade\Vario\Domain\Shared\Document\DocumentLineInterface;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxCalculationMethod;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxScheme;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxSubTotal;
use PHPUnit\Framework\TestCase;

final class DocumentTotalsCalculatorTest extends TestCase
{
    public function test_calculate_aggregates_document_totals_and_merges_same_tax_group(): void
    {
        $calculator = new DocumentTotalsCalculator();

        $result = $calculator->calculate([
            $this->createLine(
                lineExtensionAmount: 100.0,
                lineExtensionAmountTaxInclusive: 121.0,
                taxSubTotal: new DocumentTaxSubTotal(
                    calculationMethod: DocumentTaxCalculationMethod::Add,
                    scheme: DocumentTaxScheme::Vat,
                    taxableAmount: 100.0,
                    taxAmount: 21.0,
                    taxPercentage: 21.0,
                ),
            ),
            $this->createLine(
                lineExtensionAmount: 200.0,
                lineExtensionAmountTaxInclusive: 242.0,
                taxSubTotal: new DocumentTaxSubTotal(
                    calculationMethod: DocumentTaxCalculationMethod::Add,
                    scheme: DocumentTaxScheme::Vat,
                    taxableAmount: 200.0,
                    taxAmount: 42.0,
                    taxPercentage: 21.0,
                ),
            ),
        ]);

        self::assertSame(363.0, $result['monetaryTotal']->getPayableAmount());
        self::assertSame(0.0, $result['monetaryTotal']->getPayableRoundingAmount());
        self::assertSame(300.0, $result['monetaryTotal']->getTaxExclusiveAmount());
        self::assertSame(363.0, $result['monetaryTotal']->getTaxInclusiveAmount());
        self::assertSame(63.0, $result['taxTotal']->getTaxAmount());
        self::assertCount(1, $result['taxTotal']->getTaxSubTotals());
        self::assertSame(300.0, $result['taxTotal']->getTaxSubTotals()[0]->getTaxableAmount());
    }

    public function test_calculate_keeps_separate_tax_groups_when_group_key_differs(): void
    {
        $calculator = new DocumentTotalsCalculator();

        $result = $calculator->calculate([
            $this->createLine(
                lineExtensionAmount: 100.0,
                lineExtensionAmountTaxInclusive: 121.0,
                taxSubTotal: new DocumentTaxSubTotal(
                    calculationMethod: DocumentTaxCalculationMethod::Add,
                    scheme: DocumentTaxScheme::Vat,
                    taxableAmount: 100.0,
                    taxAmount: 21.0,
                    taxPercentage: 21.0,
                ),
            ),
            $this->createLine(
                lineExtensionAmount: 50.0,
                lineExtensionAmountTaxInclusive: 60.5,
                taxSubTotal: new DocumentTaxSubTotal(
                    calculationMethod: DocumentTaxCalculationMethod::Add,
                    scheme: DocumentTaxScheme::Vat,
                    taxableAmount: 50.0,
                    taxAmount: 10.5,
                    taxPercentage: 21.0,
                    taxSchemeExtensionCode: 'LOCAL-RC',
                ),
            ),
            $this->createLine(
                lineExtensionAmount: 100.0,
                lineExtensionAmountTaxInclusive: 112.0,
                taxSubTotal: new DocumentTaxSubTotal(
                    calculationMethod: DocumentTaxCalculationMethod::Add,
                    scheme: DocumentTaxScheme::Vat,
                    taxableAmount: 100.0,
                    taxAmount: 12.0,
                    taxPercentage: 12.0,
                ),
            ),
            $this->createLine(
                lineExtensionAmount: 10.0,
                lineExtensionAmountTaxInclusive: 12.1,
                taxSubTotal: new DocumentTaxSubTotal(
                    calculationMethod: DocumentTaxCalculationMethod::Total,
                    scheme: DocumentTaxScheme::Vat,
                    taxableAmount: 10.0,
                    taxAmount: 2.1,
                    taxPercentage: 21.0,
                ),
            ),
        ]);

        self::assertSame(305.6, $result['monetaryTotal']->getPayableAmount());
        self::assertSame(260.0, $result['monetaryTotal']->getTaxExclusiveAmount());
        self::assertSame(45.6, $result['taxTotal']->getTaxAmount());
        self::assertCount(4, $result['taxTotal']->getTaxSubTotals());
        self::assertSame('LOCAL-RC', $result['taxTotal']->getTaxSubTotals()[1]->getTaxSchemeExtensionCode());
        self::assertSame(
            DocumentTaxCalculationMethod::Total,
            $result['taxTotal']->getTaxSubTotals()[3]->getCalculationMethod(),
        );
    }

    public function test_calculate_supports_payable_rounding_amount(): void
    {
        $calculator = new DocumentTotalsCalculator();

        $result = $calculator->calculate([
            $this->createLine(
                lineExtensionAmount: 95.0,
                lineExtensionAmountTaxInclusive: 114.95,
                taxSubTotal: new DocumentTaxSubTotal(
                    calculationMethod: DocumentTaxCalculationMethod::Add,
                    scheme: DocumentTaxScheme::Vat,
                    taxableAmount: 95.0,
                    taxAmount: 19.95,
                    taxPercentage: 21.0,
                ),
            ),
        ], 0.05);

        self::assertSame(114.95, $result['monetaryTotal']->getTaxInclusiveAmount());
        self::assertSame(0.05, $result['monetaryTotal']->getPayableRoundingAmount());
        self::assertSame(115.0, $result['monetaryTotal']->getPayableAmount());
    }

    public function test_calculate_returns_zero_totals_for_empty_input(): void
    {
        $calculator = new DocumentTotalsCalculator();

        $result = $calculator->calculate([]);

        self::assertSame(0.0, $result['monetaryTotal']->getPayableAmount());
        self::assertSame(0.0, $result['monetaryTotal']->getPayableRoundingAmount());
        self::assertSame(0.0, $result['monetaryTotal']->getTaxExclusiveAmount());
        self::assertSame(0.0, $result['monetaryTotal']->getTaxInclusiveAmount());
        self::assertSame(0.0, $result['taxTotal']->getTaxAmount());
        self::assertSame([], $result['taxTotal']->getTaxSubTotals());
    }

    public function test_calculate_respects_custom_rounding_scale(): void
    {
        $calculator = new DocumentTotalsCalculator(scale: 3);

        $result = $calculator->calculate([
            $this->createLine(
                lineExtensionAmount: 100.5554,
                lineExtensionAmountTaxInclusive: 121.6716,
                taxSubTotal: new DocumentTaxSubTotal(
                    calculationMethod: DocumentTaxCalculationMethod::Add,
                    scheme: DocumentTaxScheme::Vat,
                    taxableAmount: 100.5554,
                    taxAmount: 21.1162,
                    taxPercentage: 21.0,
                ),
            ),
        ]);

        self::assertSame(121.672, $result['monetaryTotal']->getPayableAmount());
        self::assertSame(100.555, $result['monetaryTotal']->getTaxExclusiveAmount());
        self::assertSame(21.116, $result['taxTotal']->getTaxAmount());
    }

    private function createLine(
        float $lineExtensionAmount,
        float $lineExtensionAmountTaxInclusive,
        DocumentTaxSubTotal $taxSubTotal,
    ): DocumentLineInterface {
        return new class ($lineExtensionAmount, $lineExtensionAmountTaxInclusive, $taxSubTotal) implements DocumentLineInterface {
            public function __construct(
                private readonly float $lineExtensionAmount,
                private readonly float $lineExtensionAmountTaxInclusive,
                private readonly DocumentTaxSubTotal $taxSubTotal,
            ) {}

            public function getLineExtensionAmount(): float
            {
                return $this->lineExtensionAmount;
            }

            public function getLineExtensionAmountTaxInclusive(): float
            {
                return $this->lineExtensionAmountTaxInclusive;
            }

            public function getTaxSubTotal(): DocumentTaxSubTotal
            {
                return $this->taxSubTotal;
            }
        };
    }
}
