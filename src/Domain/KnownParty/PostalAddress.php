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
        private readonly ?string $street = null,
        private readonly ?string $buildingNumber = null,
        private readonly ?string $city = null,
        private readonly ?string $postalCode = null,
        private readonly ?string $countryIso = null
    ) {}

    /* =========================
     * Atomic values
     * ========================= */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getBuildingNumber(): ?string
    {
        return $this->buildingNumber;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getCountryIso(): ?string
    {
        return $this->countryIso;
    }

    /* =========================
     * Derived lines
     * ========================= */
    public function getStreetLine(): ?string
    {
        $street = self::normalizeWhitespace($this->street);
        $number = self::normalizeWhitespace($this->buildingNumber);

        if ($street === null && $number === null) {
            return null;
        }

        $line = trim(($street ?? '') . ' ' . ($number ?? ''));

        return $line !== '' ? $line : null;
    }

    public function getCityLine(): ?string
    {
        $postal = self::normalizeWhitespace($this->postalCode);
        $city = self::normalizeWhitespace($this->city);

        if ($postal === null && $city === null) {
            return null;
        }

        $line = trim(($postal ?? '') . ' ' . ($city ?? ''));

        return $line !== '' ? $line : null;
    }

    /* =========================
     * Display representation
     * ========================= */

    /**
     * Full normalized address.
     */
    public function getDisplayAddress(): ?string
    {
        $parts = array_filter(
            [
                $this->getStreetLine(),
                $this->getCityLine(),
                $this->getCountryIso(),
            ],
            static fn(?string $v): bool => $v !== null && $v !== ''
        );

        if ($parts === []) {
            return null;
        }

        return implode(', ', $parts);
    }

    /**
     * @return array{
     *     street:string|null,
     *     buildingNumber:string|null,
     *     streetLine:string|null,
     *     city:string|null,
     *     cityLine:string|null,
     *     postalCode:string|null,
     *     countryIso:string|null,
     *     display:string|null
     * }
     */
    public function toArray(): array
    {
        return [
            'street' => $this->street,
            'buildingNumber' => $this->buildingNumber,
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
        return $this->getDisplayAddress() ?? "";
    }

    /**
     * @return array{
     *     street:string|null,
     *     buildingNumber:string|null,
     *     streetLine:string|null,
     *     city:string|null,
     *     cityLine:string|null,
     *     postalCode:string|null,
     *     countryIso:string|null,
     *     display:string|null
     * }
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /* =========================
     * Normalization
     * ========================= */
    private static function normalizeWhitespace(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = preg_replace('/[\t]+/u', ' ', $value) ?? $value;
        $value = preg_replace('/\s{2,}/u', ' ', $value) ?? $value;

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
