<?php declare(strict_types=1);

namespace Lemonade\Vario\Domain\KnownParty;

/**
 * Interface KnownPartyInterface
 *
 * Stable public contract representing a Vario KnownParty entity.
 *
 * KnownParty describes a generic business subject within Vario ERP
 * (customer, supplier, contact, etc.). Implementations expose a
 * minimal set of stable properties guaranteed by the SDK.
 *
 * Additional API fields that are not explicitly mapped should be
 * accessible through the {@see extra()} payload to ensure forward
 * compatibility with future API changes.
 *
 * Implementations may extend this interface to provide additional
 * domain-specific behaviour.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
interface KnownPartyInterface
{
    public function getKind(): KnownPartyKind;
    public function getUuid(): string;
    public function getId(): ?string;
    public function getName(): ?string;
    public function getContactPerson(): ?string;
    public function getEmail(): ?string;
    public function getTelephone(): ?string;
    public function hasAddress(): bool;
    public function getDisplayAddress(): ?string;
    public function getStreetLine(): ?string;
    public function getCityLine(): ?string;
    public function getPostalCode(): ?string;
    public function getStreetName(): ?string;
    public function getBuildingNumber(): ?string;
    public function getPostalAddress(): ?PostalAddress;
    public function getIdentifications(): ?IdentificationCollection;
    public function hasIdentifications(): bool;
    public function getIdentificationsOrEmpty(): IdentificationCollection;
    public function getCompanyNumber(): ?string;
    public function getVatId(): ?string;

    /**
     * Returns additional API fields not mapped to explicit properties.
     *
     * @return array<string,mixed>
     */
    public function getExtra(): array;
}
