<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\KnownParty;

use Lemonade\Vario\Domain\Shared\IdentificationCollection;
use Lemonade\Vario\Domain\Shared\PostalAddress;

/**
 * Class KnownParty
 *
 * Immutable domain read model representing a business subject
 * stored in the Vario contact register.
 *
 * Instances of this class are created by the KnownPartyMapper
 * when converting raw Vario API payloads into strongly-typed
 * domain objects.
 *
 * The model intentionally exposes only stable core properties
 * such as UUID, name, contact information and postal address.
 *
 * Any additional fields returned by the API are preserved in the
 * `$extra` payload to maintain forward compatibility with newer
 * API versions or custom DatasetView configurations.
 *
 * KnownParty acts as the primary read model returned by
 * `KnownPartyApi::query()`.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrák
 * @license     MIT
 * @since       1.0
 */
final class KnownParty
{
    /**
     * @param array<string,mixed> $extra Additional unmapped API fields.
     */
    public function __construct(
        // =========================
        // ENTITY IDENTITY (required)
        // =========================
        private readonly KnownPartyKind $kind,
        private readonly string $uuid,

        // =========================
        // CORE ATTRIBUTES (optional)
        // =========================
        private readonly ?string $name = null,
        private readonly ?string $id = null,
        private readonly ?string $contactPerson = null,
        private readonly ?string $email = null,
        private readonly ?string $telephone = null,

        // =========================
        // VALUE OBJECTS
        // =========================
        private readonly ?PostalAddress $postalAddress = null,
        private readonly ?IdentificationCollection $identifications = null,

        // =========================
        // FORWARD COMPATIBILITY
        // =========================
        /** @var array<string,mixed> */
        private readonly array $extra = [],
    ) {}

    public function getKind(): KnownPartyKind
    {
        return $this->kind;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getContactPerson(): ?string
    {
        return $this->contactPerson;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function hasAddress(): bool
    {
        return $this->postalAddress !== null
            && $this->postalAddress->getDisplayAddress() !== '';
    }

    public function getPostalAddress(): ?PostalAddress
    {
        return $this->postalAddress;
    }

    public function getIdentifications(): ?IdentificationCollection
    {
        return $this->identifications;
    }

    public function getStreetLine(): ?string
    {
        return $this->postalAddress?->getStreetLine();
    }

    public function getStreetName(): ?string
    {
        return $this->postalAddress?->getStreet();
    }

    public function getBuildingNumber(): ?string
    {
        return $this->postalAddress?->getBuildingNumber();
    }

    public function getCityLine(): ?string
    {
        return $this->postalAddress?->getCityLine();
    }

    public function getPostalCode(): ?string
    {
        return $this->postalAddress?->getPostalCode();
    }

    public function getDisplayAddress(): ?string
    {
        return $this->postalAddress?->getDisplayAddress();
    }

    public function hasIdentifications(): bool
    {
        return $this->identifications !== null
            && !$this->identifications->isEmpty();
    }

    public function getIdentificationsOrEmpty(): IdentificationCollection
    {
        return $this->identifications
            ?? new IdentificationCollection([]);
    }

    public function getCompanyNumber(): ?string
    {
        return $this->getIdentificationsOrEmpty()
            ->getCompanyNumberValue();
    }

    public function getVatId(): ?string
    {
        return $this->getIdentificationsOrEmpty()
            ->getVatIdValue();
    }

    /**
     * Returns additional API fields not mapped to explicit properties.
     *
     * @return array<string,mixed>
     */
    public function getExtra(): array
    {
        return $this->extra;
    }
}
