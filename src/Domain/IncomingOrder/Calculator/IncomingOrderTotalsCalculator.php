<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Calculator;

use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderMonetaryTotal;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderTaxSubTotal;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderTaxTotal;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineInput;

/**
 * Class IncomingOrderTotalsCalculator
 *
 * Calculates document totals from already prepared low-level line inputs.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder\Calculator
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderTotalsCalculator
{
    public function __construct(
        private readonly int $scale = 2,
    ) {}

    /**
     * @param list<IncomingOrderLineInput> $lines
     * @return array{
     *     monetaryTotal: IncomingOrderMonetaryTotal,
     *     taxTotal: IncomingOrderTaxTotal
     * }
     */
    public function calculate(array $lines): array
    {
        $payableAmount = 0.0;
        $taxExclusiveAmount = 0.0;

        /** @var array<string,array{template:IncomingOrderTaxSubTotal,taxableAmount:float,taxAmount:float}> $taxGroups */
        $taxGroups = [];

        foreach ($lines as $line) {
            $taxExclusiveAmount += $line->getLineExtensionAmount();
            $payableAmount += $line->getLineExtensionAmountTaxInclusive();

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

            $taxSubTotals[] = new IncomingOrderTaxSubTotal(
                calculationMethod: $template->getCalculationMethod(),
                scheme: $template->getScheme(),
                taxableAmount: $groupTaxableAmount,
                taxAmount: $groupTaxAmount,
                taxPercentage: $template->getTaxPercentage(),
                taxSchemeExtensionCode: $template->getTaxSchemeExtensionCode(),
            );
        }

        $payableAmount = $this->round($payableAmount);
        $taxExclusiveAmount = $this->round($taxExclusiveAmount);
        $taxAmount = $this->round($taxAmount);

        return [
            'monetaryTotal' => new IncomingOrderMonetaryTotal(
                payableAmount: $payableAmount,
                payableRoundingAmount: 0.0,
                taxExclusiveAmount: $taxExclusiveAmount,
                taxInclusiveAmount: $payableAmount,
            ),
            'taxTotal' => new IncomingOrderTaxTotal(
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
