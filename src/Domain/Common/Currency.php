<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Common;

/**
 * Enum Currency
 *
 * Represents supported ISO 4217 currency codes.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Common
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
enum Currency: string
{
    case CZK = 'CZK';
    case EUR = 'EUR';
    case USD = 'USD';
    case GBP = 'GBP';
    case CHF = 'CHF';
    case PLN = 'PLN';
    case HUF = 'HUF';
    case SEK = 'SEK';
    case NOK = 'NOK';
    case DKK = 'DKK';
    case JPY = 'JPY';
    case CNY = 'CNY';
    case CAD = 'CAD';
    case AUD = 'AUD';

    public static function tryFromNullable(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }

        return self::tryFrom(strtoupper($value));
    }
}
