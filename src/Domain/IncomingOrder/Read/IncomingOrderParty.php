<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Read;

use Lemonade\Vario\Domain\Shared\IdentificationCollection;
use Lemonade\Vario\Domain\Shared\PostalAddress;

/**
 * Class IncomingOrderParty
 *
 * Immutable domain read model representing an embedded business party
 * inside an IncomingOrder document.
 *
 * This model is used for:
 *
 * - BuyerCustomerParty
 * - AccountingCustomerParty
 * - Delivery
 * - SellerSupplierParty
 *
 * Unlike KnownParty, this object does not represent a standalone
 * contact-register entity and therefore does not require identity
 * fields such as UUID, ID or Kind.
 *
 * Any additional fields returned by the API are preserved in the
 * `$extra` payload to maintain forward compatibility.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderParty
{
    /**
     * @param array<string,mixed> $extra Additional unmapped API fields.
     */
    public function __construct(
        // =========================
        // CORE ATTRIBUTES (optional)
        // =========================
        private readonly ?string $name = null,
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
        private readonly array $extra = [],
    ) {}

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
