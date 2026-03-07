<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\KnownParty;

/**
 * Enum KnownPartyKind
 *
 * Domain enumeration representing the type of a KnownParty
 * in the Vario contact register.
 *
 * The enum uses stable string identifiers for the domain layer,
 * while the Vario API expects numeric values. The `toApiValue()`
 * method converts the domain value into the integer representation
 * required by the API payload.
 *
 * Used by:
 *
 *   - KnownParty (read model)
 *   - KnownPartyInput (write model)
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrák
 * @license     MIT
 * @since       1.0
 */
enum KnownPartyKind: string
{
    case Organization = 'Organization';
    case OrganizationBranch = 'OrganizationBranch';
    case Entrepreneur = 'Entrepreneur';
    case Person = 'Person';

    public function toApiValue(): int
    {
        return match ($this) {
            self::Organization => 0,
            self::OrganizationBranch => 1,
            self::Entrepreneur => 2,
            self::Person => 3,
        };
    }
}
