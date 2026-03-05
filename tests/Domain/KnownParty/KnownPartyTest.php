<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\KnownParty;

use Lemonade\Vario\Domain\KnownParty\Identification;
use Lemonade\Vario\Domain\KnownParty\IdentificationCollection;
use Lemonade\Vario\Domain\KnownParty\IdentificationScheme;
use Lemonade\Vario\Domain\KnownParty\KnownParty;
use Lemonade\Vario\Domain\KnownParty\KnownPartyKind;
use Lemonade\Vario\Domain\KnownParty\PostalAddress;
use PHPUnit\Framework\TestCase;

final class KnownPartyTest extends TestCase
{
    public function test_company_number_helper(): void
    {
        $party = new KnownParty(
            kind: KnownPartyKind::Organization,
            uuid: 'abc',
            id: null,
            name: 'Test Company',
            contactPerson: null,
            email: null,
            telephone: null,
            postalAddress: null,
            identifications: new IdentificationCollection([
                new Identification(
                    IdentificationScheme::UIN,
                    '12345678',
                    'CZ'
                ),
            ]),
            extra: []
        );

        self::assertSame('12345678', $party->getCompanyNumber());
    }

    public function test_vat_id_helper(): void
    {
        $party = new KnownParty(
            kind: KnownPartyKind::Organization,
            uuid: 'abc',
            id: null,
            name: 'Test Company',
            contactPerson: null,
            email: null,
            telephone: null,
            postalAddress: null,
            identifications: new IdentificationCollection([
                new Identification(
                    IdentificationScheme::VAT,
                    'CZ12345678',
                    'CZ'
                ),
            ]),
            extra: []
        );

        self::assertSame('CZ12345678', $party->getVatId());
    }

    public function test_address_helpers(): void
    {
        $address = new PostalAddress(
            street: 'Main 10',
            city: 'Prague',
            postalCode: '11000',
            countryIso: 'CZ',
        );

        $party = new KnownParty(
            kind: KnownPartyKind::Organization,
            uuid: 'abc',
            name: 'Test Company',
            id: null,
            contactPerson: null,
            email: null,
            telephone: null,
            postalAddress: $address,
            identifications: new IdentificationCollection([]),
            extra: []
        );

        self::assertSame('Main 10', $party->getStreetLine());
        self::assertSame('11000 Prague', $party->getCityLine());
        self::assertSame('11000', $party->getPostalCode());
    }

    public function test_address_helpers_return_null_without_address(): void
    {
        $party = new KnownParty(
            kind: KnownPartyKind::Organization,
            uuid: 'abc',
            id: null,
            name: 'Test Company',
            contactPerson: null,
            email: null,
            telephone: null,
            postalAddress: null,
            identifications: new IdentificationCollection([]),
            extra: []
        );

        self::assertNull($party->getStreetLine());
        self::assertNull($party->getCityLine());
        self::assertNull($party->getPostalCode());
    }

    public function test_identifications_empty_collection(): void
    {
        $party = new KnownParty(
            kind: KnownPartyKind::Organization,
            uuid: 'abc',
            id: null,
            name: 'Test Company',
            contactPerson: null,
            email: null,
            telephone: null,
            postalAddress: null,
            identifications: null,
            extra: []
        );

        self::assertTrue(
            $party->getIdentificationsOrEmpty()->isEmpty()
        );
    }

    public function test_extra_payload_is_preserved(): void
    {
        $party = new KnownParty(
            kind: KnownPartyKind::Organization,
            uuid: 'abc',
            id: null,
            name: 'Test Company',
            contactPerson: null,
            email: null,
            telephone: null,
            postalAddress: null,
            identifications: null,
            extra: [
                'SomeFutureField' => 'value',
            ]
        );

        self::assertArrayHasKey(
            'SomeFutureField',
            $party->getExtra()
        );
    }
}
