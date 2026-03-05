<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\KnownParty;

use UnexpectedValueException;

/**
 * Class KnownPartyMapper
 *
 * Maps raw Vario API payloads into domain KnownParty objects.
 *
 * Acts as an anti-corruption layer between transport data
 * and the SDK domain model.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class KnownPartyMapper
{
    public function __construct(
        private KnownPartyFactoryInterface $factory,
    ) {}

    /**
     * @param array<string,mixed> $data
     */
    public function map(array $data): KnownPartyInterface
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

        return $this->factory->create(
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
        $buildingNumber = $this->nullableTrim(
            $this->stringOrNull($data['BuildingNumber'] ?? null)
        );

        $city = $this->string($data['CityName'] ?? null);
        $postalCode = $this->string($data['PostalZone'] ?? null);
        $countryIso = $this->string($data['CountryIso'] ?? null);

        if (
            $street === '' &&
            $buildingNumber === null &&
            $city === '' &&
            $postalCode === ''
        ) {
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

            $schemeValue = $this->intOrNull($item['Scheme'] ?? null);
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
                originCountry: $this->nullableTrim(
                    $this->stringOrNull($item['OriginCountry'] ?? null)
                ),
            );
        }

        return new IdentificationCollection($mapped);
    }

    /* ================= Readers ================= */

    /**
     * @param array<string,mixed> $data
     */
    private function readString(array $data, string $key): ?string
    {
        return $this->nullableTrim(
            $this->stringOrNull($data[$key] ?? null)
        );
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
        $kind = $this->intOrNull($data['Kind'] ?? null);

        return KnownPartyKind::tryFrom($kind ?? 0)
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

    private function nullableTrim(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    /* ================= Type narrowing helpers ================= */

    private function stringOrNull(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_scalar($value)) {
            return trim((string) $value);
        }

        return null;
    }

    private function string(mixed $value): string
    {
        return $this->stringOrNull($value) ?? '';
    }

    private function intOrNull(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return null;
    }
}
