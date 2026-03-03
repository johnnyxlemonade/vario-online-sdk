<?php declare(strict_types=1);

namespace Lemonade\Vario\Domain\KnownParty;

/**
 * Class DefaultKnownPartyFactory
 *
 * Default factory creating standard KnownParty instances.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain
 * @category    Domain
 * @since       1.0
 */
final class DefaultKnownPartyFactory implements KnownPartyFactoryInterface
{
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
    ): KnownPartyInterface {
        return new KnownParty(
            kind: $kind,
            uuid: $uuid,
            name: $name,
            id: $id,
            contactPerson: $contactPerson,
            email: $email,
            telephone: $telephone,
            postalAddress: $postalAddress,
            identifications: $identifications,
            extra: $extra,
        );
    }
}
