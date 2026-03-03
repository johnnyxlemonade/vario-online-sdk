<?php declare(strict_types=1);

namespace Lemonade\Vario\Domain\KnownParty;

/**
 * Class Identification
 *
 * Immutable value object representing an identification assigned
 * to a KnownParty entity (VAT ID, company number, etc.).
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
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
