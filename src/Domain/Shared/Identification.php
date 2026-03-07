<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared;

/**
 * Class Identification
 *
 * Immutable value object representing a business identification
 * assigned to a domain entity (e.g. VAT ID, company number, GLN).
 *
 * Each identification consists of a scheme (type of identifier),
 * the identifier value itself and an optional origin country.
 *
 * The scheme is represented by the IdentificationScheme enum,
 * ensuring that only supported identification types can be used.
 *
 * This value object is typically used inside IdentificationCollection
 * and attached to domain models such as KnownParty.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrák
 * @license     MIT
 * @since       1.0
 */
final class Identification
{
    public function __construct(
        private readonly IdentificationScheme $scheme,
        private readonly string $id,
        private readonly ?string $originCountry,
    ) {}

    public function getScheme(): IdentificationScheme
    {
        return $this->scheme;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getOriginCountry(): ?string
    {
        return $this->originCountry;
    }
}
