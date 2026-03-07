<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Class IdentificationCollection
 *
 * Immutable typed collection of Identification value objects.
 *
 * The collection provides convenient domain helpers for accessing
 * commonly used business identifiers such as company number (IČO)
 * and VAT ID (DIČ).
 *
 * Due to inconsistencies in real-world ERP data, some integrations
 * store VAT identifiers under the UIN scheme. The helper methods
 * therefore include fallback logic to correctly detect these values.
 *
 * The collection implements IteratorAggregate and Countable,
 * allowing it to be safely iterated and counted like a native array
 * while preserving strong typing.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrák
 * @license     MIT
 * @since       1.0
 *
 * @implements IteratorAggregate<int, Identification>
 */
final class IdentificationCollection implements IteratorAggregate, Countable
{
    /** @var list<Identification> */
    private readonly array $items;

    /**
     * @param list<Identification> $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return Traversable<int, Identification>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @return list<Identification>
     */
    public function all(): array
    {
        return $this->items;
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Returns first identification matching given scheme.
     */
    public function firstByScheme(IdentificationScheme $scheme): ?Identification
    {
        foreach ($this->items as $id) {
            if ($id->getScheme() === $scheme) {
                return $id;
            }
        }

        return null;
    }

    /**
     * Returns company number (IČO).
     *
     * Real-world ERP data may incorrectly store VAT values
     * under UIN scheme, therefore CZ-prefixed values are ignored.
     */
    public function getCompanyNumber(): ?Identification
    {
        foreach ($this->items as $id) {
            if (
                $id->getScheme() === IdentificationScheme::UIN
                && !str_starts_with($id->getId(), 'CZ')
            ) {
                return $id;
            }
        }

        return null;
    }

    /**
     * Returns VAT identification (DIČ).
     *
     * Falls back to CZ-prefixed identifiers when VAT scheme
     * is not properly used in ERP data.
     */
    public function getVatId(): ?Identification
    {
        $vat = $this->firstByScheme(IdentificationScheme::VAT);
        if ($vat !== null) {
            return $vat;
        }

        foreach ($this->items as $id) {
            if (str_starts_with($id->getId(), 'CZ')) {
                return $id;
            }
        }

        return null;
    }

    public function getCompanyNumberValue(): ?string
    {
        return $this->getCompanyNumber()?->getId();
    }

    public function getVatIdValue(): ?string
    {
        return $this->getVatId()?->getId();
    }

    /**
     * Structured representation of identifications.
     *
     * @return list<array{
     *     scheme:string,
     *     id:string,
     *     originCountry:?string
     * }>
     */
    public function toArray(): array
    {
        return array_map(
            static fn(Identification $i): array => [
                'scheme' => $i->getScheme()->name,
                'id' => $i->getId(),
                'originCountry' => $i->getOriginCountry(),
            ],
            $this->items
        );
    }
}
