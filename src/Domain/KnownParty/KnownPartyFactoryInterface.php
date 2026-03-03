<?php declare(strict_types=1);

namespace Lemonade\Vario\Domain\KnownParty;

/**
 * Interface KnownPartyFactoryInterface
 *
 * Factory responsible for creating KnownParty domain entities.
 *
 * Allows SDK users to provide custom KnownParty implementations
 * without modifying mapper logic.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
interface KnownPartyFactoryInterface
{
    /**
     * @param array<string,mixed> $extra
     */
    public function create(
        KnownPartyKind $kind,
        string $uuid,
        ?string $name,
        ?string $id,
        ?string $contactPerson,
        ?string $email,
        ?string $telephone,
        ?PostalAddress $postalAddress,
        IdentificationCollection $identifications,
        array $extra,
    ): KnownPartyInterface;
}
