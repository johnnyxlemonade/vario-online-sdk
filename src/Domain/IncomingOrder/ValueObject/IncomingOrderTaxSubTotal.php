<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\ValueObject;

use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTaxCalculationMethod;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTaxScheme;

/**
 * Class IncomingOrderTaxSubTotal
 *
 * Tax subtotal entry for IncomingOrder line items and document tax totals.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderTaxSubTotal
{
    public function __construct(
        private readonly IncomingOrderTaxCalculationMethod $calculationMethod,
        private readonly IncomingOrderTaxScheme $scheme,
        private readonly float $taxableAmount,
        private readonly float $taxAmount,
        private readonly float $taxPercentage,
        private readonly ?string $taxSchemeExtensionCode = null,
    ) {}

    public function getCalculationMethod(): IncomingOrderTaxCalculationMethod
    {
        return $this->calculationMethod;
    }

    public function getScheme(): IncomingOrderTaxScheme
    {
        return $this->scheme;
    }

    public function getTaxableAmount(): float
    {
        return $this->taxableAmount;
    }

    public function getTaxAmount(): float
    {
        return $this->taxAmount;
    }

    public function getTaxPercentage(): float
    {
        return $this->taxPercentage;
    }

    public function getTaxSchemeExtensionCode(): ?string
    {
        return $this->taxSchemeExtensionCode;
    }
}
