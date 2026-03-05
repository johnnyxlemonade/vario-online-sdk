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
 *
 * @phpstan-type KnownPartyPayload array{
 *     UUID?: string,
 *     ID?: string,
 *     Kind?: int,
 *     Name: string,
 *     ContactPerson?: string,
 *     ElectronicMail?: string,
 *     Telephone?: string,
 *     PostalAddress?: array{
 *         StreetName: string,
 *         BuildingNumber?: string,
 *         CityName: string,
 *         PostalZone: string,
 *         CountryIso: string,
 *         Formated?: string
 *     },
 *     Identifications?: list<array{
 *         Scheme: int,
 *         ID: string,
 *         OriginCountry?: string
 *     }>,
 *     TextualAttribute: list<array{
 *         LangID?: string,
 *         Name?: string,
 *         AttributeKind?: int,
 *         Value?: string
 *     }>,
 *     NumericAttribute: list<array{
 *         Name?: string,
 *         AttributeKind?: int,
 *         Value?: float|int,
 *         UnitCode?: string
 *     }>
 * }
 */
final class KnownPartyInputNormalizer
{
    /**
     * @phpstan-return KnownPartyPayload
     */
    public function normalize(KnownPartyInput $input): array
    {
        $payload = [
            'UUID' => $input->getUuid(),
            'ID' => $input->getId(),
            'Kind' => $input->getKind()?->value,
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

        // Older Vario API versions expect these collections to exist even if they are empty.
        /** @var list<array{
         *     LangID?: string,
         *     Name?: string,
         *     AttributeKind?: int,
         *     Value?: string
         * }> $textual */
        $textual = [];

        /** @var list<array{
         *     Name?: string,
         *     AttributeKind?: int,
         *     Value?: float|int,
         *     UnitCode?: string
         * }> $numeric */
        $numeric = [];

        $payload['TextualAttribute'] = $textual;
        $payload['NumericAttribute'] = $numeric;

        /** @var KnownPartyPayload $payload */
        return $payload;
    }

    /**
     * @return array{
     *     StreetName: string|null,
     *     BuildingNumber: string|null,
     *     CityName: string|null,
     *     PostalZone: string|null,
     *     CountryIso: string|null
     * }|null
     */
    private function normalizeAddress(?PostalAddress $address): ?array
    {
        if ($address === null) {
            return null;
        }

        $a = $address->toArray();

        $data = [
            'StreetName' => $a['street'],
            'CityName' => $a['city'],
            'PostalZone' => $a['postalCode'],
            'CountryIso' => $a['countryIso'],
            'BuildingNumber' => $a['buildingNumber'],
        ];

        return $data;
    }

    /**
     * @param list<Identification> $identifications
     * @return list<array{
     *     Scheme: int,
     *     ID: string,
     *     OriginCountry?: string
     * }>
     */
    private function normalizeIdentifications(array $identifications): array
    {
        $result = [];

        foreach ($identifications as $id) {
            $row = [
                'Scheme' => $id->getScheme()->value,
                'ID' => $id->getId(),
            ];

            $origin = $id->getOriginCountry();
            if ($origin !== null && $origin !== '') {
                $row['OriginCountry'] = $origin;
            }

            $result[] = $row;
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

}
