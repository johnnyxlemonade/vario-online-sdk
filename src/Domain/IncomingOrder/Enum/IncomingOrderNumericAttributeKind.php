<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Enum;

/**
 * Class IncomingOrderNumericAttributeKind
 *
 * Supported numeric attribute kinds returned by the IncomingOrder API.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
enum IncomingOrderNumericAttributeKind: string
{
    case PhysicalAttribute = 'PhysicalAttribute';
    case MeasurementDimension = 'MeasurementDimension';
    case FreeNumeric = 'FreeNumeric';
}
