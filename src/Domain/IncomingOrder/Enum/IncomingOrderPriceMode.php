<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Enum;

/**
 * Class IncomingOrderPriceMode
 *
 * Defines whether the provided line unit price
 * is tax-exclusive or tax-inclusive.
 *
 * These values are used by the IncomingOrder builder
 * and line calculator to derive line totals and tax amounts
 * from simplified developer-friendly input.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
enum IncomingOrderPriceMode: string
{
    case WithoutVat = 'WithoutVat';
    case WithVat = 'WithVat';

    public function toApiValue(): string
    {
        return $this->value;
    }
}
