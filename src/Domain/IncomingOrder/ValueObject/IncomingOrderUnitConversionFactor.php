<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\ValueObject;

use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderUnitOfMeasureScheme;

/**
 * Class IncomingOrderUnitConversionFactor
 *
 * Unit conversion factor for IncomingOrder line item payloads.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderUnitConversionFactor
{
    public function __construct(
        private readonly float $value,
        private readonly string $unitCode,
        private readonly IncomingOrderUnitOfMeasureScheme $scheme = IncomingOrderUnitOfMeasureScheme::Unknown,
    ) {}

    public function getValue(): float
    {
        return $this->value;
    }

    public function getUnitCode(): string
    {
        return $this->unitCode;
    }

    public function getScheme(): IncomingOrderUnitOfMeasureScheme
    {
        return $this->scheme;
    }
}
