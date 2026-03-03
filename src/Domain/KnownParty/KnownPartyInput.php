<?php declare(strict_types=1);

namespace Lemonade\Vario\Domain\KnownParty;

/**
 * Class KnownPartyInput
 *
 * Mutable input model used for creating or updating
 * KnownParty entities via the Vario API.
 *
 * This object represents user intent rather than
 * a persisted domain state.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class KnownPartyInput
{
    private string $name;

    private ?string $contactPerson = null;
    private ?string $email = null;
    private ?string $telephone = null;

    private ?PostalAddress $address = null;

    /** @var list<Identification> */
    private array $identifications = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    // -------------------------
    // getters
    // -------------------------

    public function getName(): string
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

    public function getAddress(): ?PostalAddress
    {
        return $this->address;
    }

    /**
     * @return list<Identification>
     */
    public function getIdentifications(): array
    {
        return $this->identifications;
    }

    // -------------------------
    // fluent modifiers
    // -------------------------

    public function withContactPerson(?string $contactPerson): self
    {
        $this->contactPerson = $contactPerson;
        return $this;
    }

    public function withEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function withTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function withAddress(?PostalAddress $address): self
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @param list<Identification> $identifications
     */
    public function withIdentifications(array $identifications): self
    {
        $this->identifications = $identifications;
        return $this;
    }

    public function addIdentification(Identification $identification): self
    {
        $this->identifications[] = $identification;
        return $this;
    }
}
