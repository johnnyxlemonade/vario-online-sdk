<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared;

/**
 * Enum IdentificationScheme
 *
 * Domain enumeration describing the type of business identifier
 * assigned to an entity (e.g. company number, VAT ID, GLN).
 *
 * The domain layer uses stable string identifiers while the
 * Vario API expects numeric codes. The `toApiValue()` method
 * converts the domain representation into the integer value
 * required by the API payload.
 *
 * Typical schemes include:
 *
 * - UIN  → company number (IČO)
 * - VAT  → VAT identification (DIČ)
 * - GLN  → Global Location Number
 * - BIC  → Bank Identifier Code
 *
 * This enum is used by the Identification value object and
 * related domain collections.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrák
 * @license     MIT
 * @since       1.0
 */
enum IdentificationScheme: string
{
    case UIN = 'UIN';
    case VAT = 'VAT';
    case GLN = 'GLN';
    case BIC = 'BIC';
    case UUID = 'UUID';
    case ISID = 'ISID';
    case OTHER = 'OTHER';

    public function toApiValue(): int
    {
        return match ($this) {
            self::UIN => 0,
            self::VAT => 1,
            self::GLN => 2,
            self::BIC => 3,
            self::UUID => 90,
            self::ISID => 91,
            self::OTHER => 99,
        };
    }
}
