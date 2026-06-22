<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\Calculator;

use InvalidArgumentException;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentPriceMode;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentQuantity;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxSubTotal;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineInput;

final class DocumentLineCalculator
{
    public function __construct(
        private readonly int $scale = 2,
    ) {}

    /**
     * @template TLineItem of object
     * @param DocumentCalculatedLineInput<TLineItem> $input
     * @return array{
     *     lineExtensionAmount: float,
     *     lineExtensionAmountTaxInclusive: float,
     *     lineQuantity: DocumentQuantity,
     *     taxSubTotal: DocumentTaxSubTotal
     * }
     */
    public function calculate(DocumentCalculatedLineInput $input): array
    {
        $quantity = $input->getQuantity();
        $unitPrice = $input->getUnitPrice();
        $vatRate = $input->getVatRate();

        if ($quantity <= 0.0) {
            throw new InvalidArgumentException('Document line quantity must be greater than zero.');
        }

        if ($unitPrice < 0.0) {
            throw new InvalidArgumentException('Document unit price must not be negative.');
        }

        if ($vatRate < 0.0) {
            throw new InvalidArgumentException('Document VAT rate must not be negative.');
        }

        if ($input->getPriceMode() === DocumentPriceMode::WithoutVat) {
            $lineExtensionAmount = $this->round($quantity * $unitPrice);
            $taxAmount = $this->round($lineExtensionAmount * ($vatRate / 100));
            $lineExtensionAmountTaxInclusive = $this->round($lineExtensionAmount + $taxAmount);
        } else {
            $lineExtensionAmountTaxInclusive = $this->round($quantity * $unitPrice);
            $lineExtensionAmount = $this->round($lineExtensionAmountTaxInclusive / (1 + ($vatRate / 100)));
            $taxAmount = $this->round($lineExtensionAmountTaxInclusive - $lineExtensionAmount);
        }

        return [
            'lineExtensionAmount' => $lineExtensionAmount,
            'lineExtensionAmountTaxInclusive' => $lineExtensionAmountTaxInclusive,
            'lineQuantity' => new DocumentQuantity(
                value: $quantity,
                unitCode: $input->getUnitCode(),
                scheme: $input->getUnitScheme(),
            ),
            'taxSubTotal' => new DocumentTaxSubTotal(
                calculationMethod: $input->getTaxCalculationMethod(),
                scheme: $input->getTaxScheme(),
                taxableAmount: $lineExtensionAmount,
                taxAmount: $taxAmount,
                taxPercentage: $vatRate,
                taxSchemeExtensionCode: $input->getTaxSchemeExtensionCode(),
            ),
        ];
    }

    private function round(float $value): float
    {
        return round($value, $this->scale, PHP_ROUND_HALF_UP);
    }
}
