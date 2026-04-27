<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Enum;

/**
 * Class IncomingOrderUnitOfMeasureScheme
 *
 * Supported unit of measure schemes for IncomingOrder write payloads.
 *
 * These values follow the string representation used by the
 * real Vario IncomingOrder API examples and support payloads.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
enum IncomingOrderUnitOfMeasureScheme: string
{
    case Unknown = 'Unknown';
    case SI = 'SI';
    case ImperialGB = 'ImperialGB';
    case ImperialUS = 'ImperialUS';

    public function toApiValue(): string
    {
        return $this->value;
    }
}
