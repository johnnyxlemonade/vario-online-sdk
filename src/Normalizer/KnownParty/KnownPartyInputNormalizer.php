<?php

declare(strict_types=1);

namespace Lemonade\Vario\Normalizer\KnownParty;

use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\Shared\Identification;
use Lemonade\Vario\Domain\Shared\PostalAddress;

/**
 * Class KnownPartyInputNormalizer
 *
 * Domain → transport normalizer converting KnownPartyInput
 * objects into Vario API request payloads.
 *
 * The normalizer acts as the write-side anti-corruption layer
 * between the SDK domain model and the external Vario API format.
 *
 * Responsibilities:
 *
 * - converting domain value objects into API-compatible arrays
 * - mapping enums to numeric API codes
 * - serializing nested value objects (PostalAddress, Identification)
 * - filtering null / empty values from payloads
 * - ensuring compatibility with legacy Vario API requirements
 *
 * The resulting payload structure matches the format expected by:
 *
 *     KnownPartyApi::upsert()
 *
 * and can also be inspected safely via:
 *
 *     KnownPartyApi::previewUpsert()
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Normalizer
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrák
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
            'Kind' => $input->getKind()?->toApiValue(),
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
                'Scheme' => $id->getScheme()->toApiValue(),
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
            static fn($v): bool => $v !== null && $v !== ''
        );
    }

}
