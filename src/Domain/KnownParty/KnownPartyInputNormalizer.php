<?php declare(strict_types=1);

namespace Lemonade\Vario\Domain\KnownParty;

/**
 * Class KnownPartyInputNormalizer
 *
 * Converts KnownPartyInput domain objects into
 * Vario API request payloads.
 *
 * Acts as write-side anti-corruption layer.
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class KnownPartyInputNormalizer
{
    /**
     * @return array<string,mixed>
     */
    public function normalize(KnownPartyInput $input): array
    {
        $payload = [
            'Name' => $input->getName(),
        ];

        $payload += $this->filterNullable([
            'ContactPerson' => $input->getContactPerson(),
            'ElectronicMail' => $input->getEmail(),
            'Telephone' => $input->getTelephone(),
            'PostalAddress' => $this->normalizeAddress($input->getAddress()),
        ]);

        $identifications = $this->normalizeIdentifications(
            $input->getIdentifications()
        );

        if ($identifications !== []) {
            $payload['Identifications'] = $identifications;
        }

        return $payload;
    }

    /**
     * @return array<string,mixed>|null
     */
    private function normalizeAddress(?PostalAddress $address): ?array
    {
        if ($address === null) {
            return null;
        }

        $formatted = $address->getFormatted()
            ?? $address->getDisplayAddress();

        return $this->filterEmptyStrings([
            'StreetName' => $address->getStreet(),
            'CityName'   => $address->getCity(),
            'PostalZone' => $address->getPostalCode(),
            'CountryIso' => $address->getCountryIso(),
            'Formated'   => $formatted,
        ]);
    }

    /**
     * @param list<Identification> $identifications
     * @return list<array<string,mixed>>
     */
    private function normalizeIdentifications(array $identifications): array
    {
        $result = [];

        foreach ($identifications as $id) {
            $result[] = $this->filterNullable([
                'Scheme' => $id->getScheme()->value,
                'ID' => $id->getId(),
                'OriginCountry' => $id->getOriginCountry(),
            ]);
        }

        return $result;
    }

    /**
     * Removes null and empty-string values.
     *
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    private function filterNullable(array $data): array
    {
        return array_filter(
            $data,
            static fn ($v): bool => $v !== null && $v !== ''
        );
    }

    /**
     * Removes empty strings from string-only arrays.
     *
     * @param array<string,string> $data
     * @return array<string,string>
     */
    private function filterEmptyStrings(array $data): array
    {
        return array_filter(
            $data,
            static fn (string $v): bool => $v !== ''
        );
    }
}
