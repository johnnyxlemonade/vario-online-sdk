<?php declare(strict_types=1);

namespace Lemonade\Vario\Domain\KnownParty;

/**
 * Enum KnownPartyKind
 *
 * Defines the type of known party in Vario contact register.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain
 * @category    Domain
 */
enum KnownPartyKind: int
{
    case Organization = 0;
    case OrganizationBranch = 1;
    case Entrepreneur = 2;
    case Person = 3;
}
