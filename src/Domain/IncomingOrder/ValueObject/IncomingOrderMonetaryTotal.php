<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\ValueObject;

/**
 * Class IncomingOrderMonetaryTotal
 *
 * Monetary total expressed in document currency for IncomingOrder payloads.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderMonetaryTotal
{
    public function __construct(
        private readonly float $payableAmount,
        private readonly float $payableRoundingAmount,
        private readonly float $taxExclusiveAmount,
        private readonly float $taxInclusiveAmount,
    ) {}

    public function getPayableAmount(): float
    {
        return $this->payableAmount;
    }

    public function getPayableRoundingAmount(): float
    {
        return $this->payableRoundingAmount;
    }

    public function getTaxExclusiveAmount(): float
    {
        return $this->taxExclusiveAmount;
    }

    public function getTaxInclusiveAmount(): float
    {
        return $this->taxInclusiveAmount;
    }
}
