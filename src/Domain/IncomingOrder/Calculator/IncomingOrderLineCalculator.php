<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Calculator;

use InvalidArgumentException;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPriceMode;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderQuantity;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderTaxSubTotal;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderCalculatedLineInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineInput;

/**
 * Class IncomingOrderLineCalculator
 *
 * Calculates a low-level IncomingOrderLineInput from a high-level
 * developer-friendly line definition.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder\Calculator
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderLineCalculator
{
    public function __construct(
        private readonly int $scale = 2,
    ) {}

    public function calculate(IncomingOrderCalculatedLineInput $input): IncomingOrderLineInput
    {
        $quantity = $input->getQuantity();
        $unitPrice = $input->getUnitPrice();
        $vatRate = $input->getVatRate();

        if ($quantity <= 0.0) {
            throw new InvalidArgumentException('IncomingOrder line quantity must be greater than zero.');
        }

        if ($unitPrice < 0.0) {
            throw new InvalidArgumentException('IncomingOrder unit price must not be negative.');
        }

        if ($vatRate < 0.0) {
            throw new InvalidArgumentException('IncomingOrder VAT rate must not be negative.');
        }

        if ($input->getPriceMode() === IncomingOrderPriceMode::WithoutVat) {
            $lineExtensionAmount = $this->round($quantity * $unitPrice);
            $taxAmount = $this->round($lineExtensionAmount * ($vatRate / 100));
            $lineExtensionAmountTaxInclusive = $this->round($lineExtensionAmount + $taxAmount);
        } else {
            $lineExtensionAmountTaxInclusive = $this->round($quantity * $unitPrice);
            $lineExtensionAmount = $this->round($lineExtensionAmountTaxInclusive / (1 + ($vatRate / 100)));
            $taxAmount = $this->round($lineExtensionAmountTaxInclusive - $lineExtensionAmount);
        }

        return new IncomingOrderLineInput(
            uuid: $input->getUuid(),
            lineExtensionAmount: $lineExtensionAmount,
            lineExtensionAmountTaxInclusive: $lineExtensionAmountTaxInclusive,
            lineItem: $input->getLineItem(),
            lineQuantity: new IncomingOrderQuantity(
                value: $quantity,
                unitCode: $input->getUnitCode(),
                scheme: $input->getUnitScheme(),
            ),
            taxSubTotal: new IncomingOrderTaxSubTotal(
                calculationMethod: $input->getTaxCalculationMethod(),
                scheme: $input->getTaxScheme(),
                taxableAmount: $lineExtensionAmount,
                taxAmount: $taxAmount,
                taxPercentage: $vatRate,
                taxSchemeExtensionCode: $input->getTaxSchemeExtensionCode(),
            ),
            id: $input->getId(),
            note: $input->getNote(),
        );
    }

    private function round(float $value): float
    {
        return round($value, $this->scale, PHP_ROUND_HALF_UP);
    }
}
