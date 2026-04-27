<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\ValueObject;

/**
 * Class IncomingOrderTaxTotal
 *
 * Document-level tax summary for IncomingOrder payloads.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderTaxTotal
{
    /**
     * @param list<IncomingOrderTaxSubTotal> $taxSubTotals
     */
    public function __construct(
        private readonly float $taxAmount,
        private readonly array $taxSubTotals,
    ) {}

    public function getTaxAmount(): float
    {
        return $this->taxAmount;
    }

    /**
     * @return list<IncomingOrderTaxSubTotal>
     */
    public function getTaxSubTotals(): array
    {
        return $this->taxSubTotals;
    }
}
