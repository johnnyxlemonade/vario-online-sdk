<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\KnownParty;

use Lemonade\Vario\Domain\KnownParty\KnownParty;
use Lemonade\Vario\Domain\KnownParty\KnownPartyKind;
use Lemonade\Vario\Domain\Shared\IdentificationCollection;
use Lemonade\Vario\Domain\Shared\PostalAddress;
use PHPUnit\Framework\TestCase;

final class KnownPartyTest extends TestCase
{
    public function test_creates_known_party_instance(): void
    {
        $address = new PostalAddress(
            street: 'Main 10',
            city: 'Prague',
            postalCode: '11000',
            countryIso: 'CZ'
        );

        $identifications = new IdentificationCollection([]);

        $party = new KnownParty(
            kind: KnownPartyKind::Organization,
            uuid: 'abc-123',
            name: 'Test Company',
            id: 'CUST001',
            contactPerson: 'John Doe',
            email: 'john@example.com',
            telephone: '123456',
            postalAddress: $address,
            identifications: $identifications,
            extra: ['foo' => 'bar']
        );

        self::assertSame('abc-123', $party->getUuid());
        self::assertSame('Test Company', $party->getName());
        self::assertSame('CUST001', $party->getId());
        self::assertSame('John Doe', $party->getContactPerson());
        self::assertSame('john@example.com', $party->getEmail());
        self::assertSame('123456', $party->getTelephone());

        self::assertSame('foo', array_key_first($party->getExtra()));
    }

    public function testAddressHelpers(): void
    {
        $address = new PostalAddress(
            street: 'Main',
            city: 'Prague',
            postalCode: '11000',
            countryIso: 'CZ',
            buildingNumber: '10'
        );

        $party = new KnownParty(
            kind: KnownPartyKind::Organization,
            uuid: 'abc',
            postalAddress: $address
        );

        self::assertTrue($party->hasAddress());
        self::assertSame('Main 10', $party->getStreetLine());
        self::assertSame('Main', $party->getStreetName());
        self::assertSame('10', $party->getBuildingNumber());
        self::assertSame('11000 Prague', $party->getCityLine());
        self::assertSame('11000', $party->getPostalCode());
        self::assertSame('Main 10, 11000 Prague, CZ', $party->getDisplayAddress());
    }

    public function testAddressHelpersReturnNullWhenAddressMissing(): void
    {
        $party = new KnownParty(
            kind: KnownPartyKind::Organization,
            uuid: 'abc'
        );

        self::assertFalse($party->hasAddress());
        self::assertNull($party->getStreetLine());
        self::assertNull($party->getStreetName());
        self::assertNull($party->getBuildingNumber());
        self::assertNull($party->getCityLine());
        self::assertNull($party->getPostalCode());
        self::assertNull($party->getDisplayAddress());
    }

    public function testIdentificationHelpers(): void
    {
        $ids = new IdentificationCollection([]);

        $party = new KnownParty(
            kind: KnownPartyKind::Organization,
            uuid: 'abc',
            identifications: $ids
        );

        self::assertFalse($party->hasIdentifications());
        self::assertInstanceOf(IdentificationCollection::class, $party->getIdentificationsOrEmpty());
    }

    public function testCompanyNumberAndVatHelpersReturnNullWhenMissing(): void
    {
        $party = new KnownParty(
            kind: KnownPartyKind::Organization,
            uuid: 'abc'
        );

        self::assertNull($party->getCompanyNumber());
        self::assertNull($party->getVatId());
    }

    public function testGetKind(): void
    {
        $party = new KnownParty(
            kind: KnownPartyKind::Person,
            uuid: 'abc'
        );

        self::assertSame(KnownPartyKind::Person, $party->getKind());
    }

    public function testGetIdentifications(): void
    {
        $ids = new IdentificationCollection([]);

        $party = new KnownParty(
            kind: KnownPartyKind::Organization,
            uuid: 'abc',
            identifications: $ids
        );

        self::assertSame($ids, $party->getIdentifications());
    }

    public function testGetIdentificationsReturnsNullWhenMissing(): void
    {
        $party = new KnownParty(
            kind: KnownPartyKind::Organization,
            uuid: 'abc'
        );

        self::assertNull($party->getIdentifications());
    }
}
