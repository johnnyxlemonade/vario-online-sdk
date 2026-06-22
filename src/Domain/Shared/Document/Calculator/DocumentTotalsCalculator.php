<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\Calculator;

use Lemonade\Vario\Domain\Shared\Document\DocumentLineInterface;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentMonetaryTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxSubTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxTotal;

final class DocumentTotalsCalculator
{
    public function __construct(
        private readonly int $scale = 2,
    ) {}

    /**
     * @param list<DocumentLineInterface> $lines
     * @return array{
     *     monetaryTotal: DocumentMonetaryTotal,
     *     taxTotal: DocumentTaxTotal
     * }
     */
    public function calculate(array $lines, float $payableRoundingAmount = 0.0): array
    {
        $taxInclusiveAmount = 0.0;
        $taxExclusiveAmount = 0.0;

        /** @var array<string,array{template:DocumentTaxSubTotal,taxableAmount:float,taxAmount:float}> $taxGroups */
        $taxGroups = [];

        foreach ($lines as $line) {
            $taxExclusiveAmount += $line->getLineExtensionAmount();
            $taxInclusiveAmount += $line->getLineExtensionAmountTaxInclusive();

            $taxSubTotal = $line->getTaxSubTotal();

            $groupKey = implode('|', [
                $taxSubTotal->getCalculationMethod()->value,
                $taxSubTotal->getScheme()->value,
                (string) $taxSubTotal->getTaxPercentage(),
                ($taxSubTotal->getTaxSchemeExtensionCode() ?? ''),
            ]);

            if (!isset($taxGroups[$groupKey])) {
                $taxGroups[$groupKey] = [
                    'template' => $taxSubTotal,
                    'taxableAmount' => 0.0,
                    'taxAmount' => 0.0,
                ];
            }

            $taxGroups[$groupKey]['taxableAmount'] += $taxSubTotal->getTaxableAmount();
            $taxGroups[$groupKey]['taxAmount'] += $taxSubTotal->getTaxAmount();
        }

        $taxSubTotals = [];
        $taxAmount = 0.0;

        foreach ($taxGroups as $group) {
            $template = $group['template'];
            $groupTaxableAmount = $this->round($group['taxableAmount']);
            $groupTaxAmount = $this->round($group['taxAmount']);

            $taxAmount += $groupTaxAmount;

            $taxSubTotals[] = new DocumentTaxSubTotal(
                calculationMethod: $template->getCalculationMethod(),
                scheme: $template->getScheme(),
                taxableAmount: $groupTaxableAmount,
                taxAmount: $groupTaxAmount,
                taxPercentage: $template->getTaxPercentage(),
                taxSchemeExtensionCode: $template->getTaxSchemeExtensionCode(),
            );
        }

        $taxInclusiveAmount = $this->round($taxInclusiveAmount);
        $taxExclusiveAmount = $this->round($taxExclusiveAmount);
        $payableRoundingAmount = $this->round($payableRoundingAmount);
        $taxAmount = $this->round($taxAmount);
        $payableAmount = $this->round($taxInclusiveAmount + $payableRoundingAmount);

        return [
            'monetaryTotal' => new DocumentMonetaryTotal(
                payableAmount: $payableAmount,
                payableRoundingAmount: $payableRoundingAmount,
                taxExclusiveAmount: $taxExclusiveAmount,
                taxInclusiveAmount: $taxInclusiveAmount,
            ),
            'taxTotal' => new DocumentTaxTotal(
                taxAmount: $taxAmount,
                taxSubTotals: $taxSubTotals,
            ),
        ];
    }

    private function round(float $value): float
    {
        return round($value, $this->scale, PHP_ROUND_HALF_UP);
    }
}
