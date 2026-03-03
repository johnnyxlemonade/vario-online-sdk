<?php declare(strict_types=1);

namespace Lemonade\Vario\Domain\KnownParty;

use Stringable;
use JsonSerializable;

/**
 * Class PostalAddress
 *
 * Immutable value object representing a postal address
 * assigned to a KnownParty entity.
 *
 * Normalizes Vario API address formatting into a stable
 * single-line representation suitable for UI and logging.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class PostalAddress implements Stringable, JsonSerializable
{
    public function __construct(
        private readonly string $street,
        private readonly string $city,
        private readonly string $postalCode,
        private readonly string $countryIso,
        private readonly ?string $formatted = null,
    ) {}

    /* =========================
     * Atomic values
     * ========================= */
    public function getStreet(): string
    {
        return $this->street;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCountryIso(): string
    {
        return $this->countryIso;
    }

    public function getFormatted(): ?string
    {
        return $this->formatted;
    }

    /* =========================
     * Derived lines
     * ========================= */

    public function getStreetLine(): string
    {
        return self::normalizeWhitespace($this->street);
    }

    public function getCityLine(): string
    {
        return self::normalizeWhitespace(
            trim($this->postalCode . ' ' . $this->city)
        );
    }

    /* =========================
     * Display representation
     * ========================= */

    /**
     * Full normalized address.
     */
    public function getDisplayAddress(): string
    {
        if ($this->formatted !== null && trim($this->formatted) !== '') {
            return self::normalizeAddress($this->formatted);
        }

        $parts = array_filter(
            [
                $this->getStreetLine(),
                $this->getCityLine(),
                $this->countryIso,
            ],
            static fn (string $v): bool => $v !== ''
        );

        return implode(', ', $parts);
    }

    /**
     * Structured representation.
     *
     * @return array{
     *     street:string,
     *     streetLine:string,
     *     city:string,
     *     cityLine:string,
     *     postalCode:string,
     *     countryIso:string,
     *     display:string
     * }
     */
    public function toArray(): array
    {
        return [
            'street' => $this->street,
            'streetLine' => $this->getStreetLine(),
            'city' => $this->city,
            'cityLine' => $this->getCityLine(),
            'postalCode' => $this->postalCode,
            'countryIso' => $this->countryIso,
            'display' => $this->getDisplayAddress(),
        ];
    }

    public function __toString(): string
    {
        return $this->getDisplayAddress();
    }

    /**
     * JSON representation of address.
     *
     * @return array<string,string>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /* =========================
     * Normalization
     * ========================= */

    private static function normalizeAddress(string $value): string
    {
        $value = preg_replace('/\R+/u', ', ', $value) ?? $value;

        return self::normalizeWhitespace($value);
    }

    private static function normalizeWhitespace(string $value): string
    {
        $value = preg_replace('/[\t]+/u', ' ', $value) ?? $value;
        $value = preg_replace('/\s{2,}/u', ' ', $value) ?? $value;

        return trim($value);
    }
}
