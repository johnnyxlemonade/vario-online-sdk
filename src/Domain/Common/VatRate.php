<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Common;

/**
 * Enum VatRate
 *
 * Represents VAT rate categories used by Vario ERP.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Common
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
enum VatRate: string
{
    case STANDARD = 'Základní';
    case REDUCED = 'Snížená';
    case SECOND_REDUCED = 'Druhá snížená';

    public static function tryFromNullable(?string $value): ?self
    {
        return $value !== null ? self::tryFrom($value) : null;
    }

    public function percentage(): float
    {
        return match ($this) {
            self::STANDARD => 21.0,
            self::REDUCED => 12.0,
            self::SECOND_REDUCED => 10.0,
        };
    }
}
