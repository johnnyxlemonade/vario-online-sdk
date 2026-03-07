<?php

declare(strict_types=1);

namespace Lemonade\Vario\Mapper\KnownParty;

use Lemonade\Vario\Domain\KnownParty\KnownParty;
use Lemonade\Vario\Domain\KnownParty\KnownPartyKind;
use Lemonade\Vario\Domain\Shared\Identification;
use Lemonade\Vario\Domain\Shared\IdentificationCollection;
use Lemonade\Vario\Domain\Shared\IdentificationScheme;
use Lemonade\Vario\Domain\Shared\PostalAddress;
use Lemonade\Vario\Mapper\Support\ScalarReadersTrait;
use UnexpectedValueException;

/**
 * Class KnownPartyMapper
 *
 * Transport → Domain mapper converting raw Vario API payloads
 * into strongly typed KnownParty domain objects.
 *
 * The mapper acts as an anti-corruption layer between the external
 * Vario API transport format and the internal SDK domain model.
 * It is responsible for:
 *
 * - extracting known fields from the API payload
 * - normalizing scalar values
 * - constructing value objects (PostalAddress, Identification, etc.)
 * - preserving unknown fields for forward compatibility
 *
 * Any fields not explicitly mapped are stored in the `$extra`
 * payload of the KnownParty entity.
 *
 * The mapper is used internally by:
 *
 *     KnownPartyApi::query()
 *
 * to transform API responses into immutable domain models.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Mapper
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrák
 * @license     MIT
 * @since       1.0
 */
final class KnownPartyMapper
{
    use ScalarReadersTrait;

    /**
     * @param array<string,mixed> $data
     */
    public function map(array $data): KnownParty
    {
        /** ---------- Address ---------- */
        $addressRaw = $data['PostalAddress'] ?? null;

        /** @var array<string,mixed>|null $addressData */
        $addressData = is_array($addressRaw)
            ? $addressRaw
            : null;

        $address = $this->mapAddress($addressData);

        /** ---------- Identifications ---------- */
        $identificationsRaw = $data['Identifications'] ?? null;

        /** @var array<int,array<string,mixed>> $identificationsData */
        $identificationsData = is_array($identificationsRaw)
            ? $identificationsRaw
            : [];

        $identifications = $this->mapIdentifications($identificationsData);

        /** ---------- Forward compatibility payload ---------- */
        /** @var array<string,mixed> $extra */
        $extra = array_diff_key($data, [
            'ID' => true,
            'Name' => true,
            'Kind' => true,
            'UUID' => true,
            'ContactPerson' => true,
            'ElectronicMail' => true,
            'Telephone' => true,
            'PostalAddress' => true,
            'Identifications' => true,
        ]);

        return new KnownParty(
            kind: $this->readKind($data),
            uuid: $this->readRequiredString($data, 'UUID'),
            id: $this->readString($data, 'ID'),
            name: $this->resolveName($data),
            contactPerson: $this->readString($data, 'ContactPerson'),
            email: $this->readString($data, 'ElectronicMail'),
            telephone: $this->readString($data, 'Telephone'),
            postalAddress: $address,
            identifications: $identifications,
            extra: $extra,
        );
    }

    /**
     * @param array<string,mixed>|null $data
     */
    private function mapAddress(?array $data): ?PostalAddress
    {
        if ($data === null || $data === []) {
            return null;
        }

        $street = $this->string($data['StreetName'] ?? null);
        $buildingNumber = $this->stringOrNull($data['BuildingNumber'] ?? null);

        $city = $this->string($data['CityName'] ?? null);
        $postalCode = $this->string($data['PostalZone'] ?? null);
        $countryIso = $this->string($data['CountryIso'] ?? null);

        if ($street === '' && $buildingNumber === null && $city === '' && $postalCode === '') {
            return null;
        }

        return new PostalAddress(
            street: $street,
            city: $city,
            postalCode: $postalCode,
            countryIso: $countryIso,
            buildingNumber: $buildingNumber
        );
    }

    /**
     * @param array<int,array<string,mixed>> $items
     */
    private function mapIdentifications(array $items): IdentificationCollection
    {
        $mapped = [];

        foreach ($items as $item) {
            $schemeValue = $this->stringOrNull($item['Scheme'] ?? null);
            $id = $this->stringOrNull($item['ID'] ?? null);

            if ($schemeValue === null || $id === null) {
                continue;
            }

            $scheme = IdentificationScheme::tryFrom($schemeValue);

            if ($scheme === null) {
                continue;
            }

            $mapped[] = new Identification(
                scheme: $scheme,
                id: $id,
                originCountry: $this->stringOrNull($item['OriginCountry'] ?? null),
            );
        }

        return new IdentificationCollection($mapped);
    }

    /**
     * @param array<string,mixed> $data
     */
    private function readString(array $data, string $key): ?string
    {
        return $this->stringOrNull($data[$key] ?? null);
    }

    /**
     * @param array<string,mixed> $data
     */
    private function readRequiredString(array $data, string $key): string
    {
        $value = $this->readString($data, $key);

        if ($value === null) {
            throw new UnexpectedValueException(
                sprintf('KnownParty payload missing required field "%s".', $key)
            );
        }

        return $value;
    }

    /**
     * @param array<string,mixed> $data
     */
    private function readKind(array $data): KnownPartyKind
    {
        $kind = $this->stringOrNull($data['Kind'] ?? null);

        return KnownPartyKind::tryFrom($kind ?? 'Organization')
            ?? KnownPartyKind::Organization;
    }

    /**
     * @param array<string,mixed> $data
     */
    private function resolveName(array $data): ?string
    {
        foreach (['Name', 'ContactPerson'] as $field) {
            $value = $this->readString($data, $field);

            if ($value !== null) {
                return $value;
            }
        }

        return $this->readString($data, 'ID');
    }

}
