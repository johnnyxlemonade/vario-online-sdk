<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Read;

use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentQuantity;

/**
 * Class IncomingOrderUnitPrice
 *
 * Immutable domain read model representing line unit price
 * returned by the IncomingOrder API.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderUnitPrice
{
    /**
     * @param array<string,mixed> $extra Additional unmapped API fields.
     */
    public function __construct(
        private readonly ?float $amount = null,
        private readonly ?float $amountTaxInclusive = null,
        private readonly ?DocumentQuantity $quantity = null,
        private readonly array $extra = [],
    ) {}

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function getAmountTaxInclusive(): ?float
    {
        return $this->amountTaxInclusive;
    }

    public function getQuantity(): ?DocumentQuantity
    {
        return $this->quantity;
    }

    /**
     * @return array<string,mixed>
     */
    public function getExtra(): array
    {
        return $this->extra;
    }
}
