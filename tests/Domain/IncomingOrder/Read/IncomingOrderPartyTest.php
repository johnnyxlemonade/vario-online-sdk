<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Read;

use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderParty;
use Lemonade\Vario\Domain\Shared\Identification;
use Lemonade\Vario\Domain\Shared\IdentificationCollection;
use Lemonade\Vario\Domain\Shared\IdentificationScheme;
use Lemonade\Vario\Domain\Shared\PostalAddress;
use PHPUnit\Framework\TestCase;

final class IncomingOrderPartyTest extends TestCase
{
    public function test_it_exposes_all_values_and_delegates_address_and_identifications(): void
    {
        $address = new PostalAddress(
            street: 'Vodná',
            buildingNumber: '57',
            city: 'Žabovřesky',
            postalCode: '566 00',
            countryIso: 'CZ',
        );

        $identifications = new IdentificationCollection([
            new Identification(
                scheme: IdentificationScheme::UIN,
                id: '89745612',
                originCountry: 'CZ',
            ),
            new Identification(
                scheme: IdentificationScheme::VAT,
                id: 'CZ89745612',
                originCountry: 'CZ',
            ),
        ]);

        $party = new IncomingOrderParty(
            name: '1. česká podvodná',
            contactPerson: 'Rybana Wassermannová',
            email: 'pod.vodnik@zaby.cz',
            telephone: '+420557788996',
            postalAddress: $address,
            identifications: $identifications,
            extra: [
                'CustomField' => 'custom-value',
            ],
        );

        self::assertSame('1. česká podvodná', $party->getName());
        self::assertSame('Rybana Wassermannová', $party->getContactPerson());
        self::assertSame('pod.vodnik@zaby.cz', $party->getEmail());
        self::assertSame('+420557788996', $party->getTelephone());

        self::assertTrue($party->hasAddress());
        self::assertSame($address, $party->getPostalAddress());
        self::assertSame($address->getStreetLine(), $party->getStreetLine());
        self::assertSame($address->getStreet(), $party->getStreetName());
        self::assertSame($address->getBuildingNumber(), $party->getBuildingNumber());
        self::assertSame($address->getCityLine(), $party->getCityLine());
        self::assertSame($address->getPostalCode(), $party->getPostalCode());
        self::assertSame($address->getDisplayAddress(), $party->getDisplayAddress());

        self::assertTrue($party->hasIdentifications());
        self::assertSame($identifications, $party->getIdentifications());
        self::assertSame($identifications, $party->getIdentificationsOrEmpty());
        self::assertSame($identifications->getCompanyNumberValue(), $party->getCompanyNumber());
        self::assertSame($identifications->getVatIdValue(), $party->getVatId());

        self::assertSame([
            'CustomField' => 'custom-value',
        ], $party->getExtra());
    }

    public function test_it_supports_empty_state(): void
    {
        $party = new IncomingOrderParty();

        self::assertNull($party->getName());
        self::assertNull($party->getContactPerson());
        self::assertNull($party->getEmail());
        self::assertNull($party->getTelephone());

        self::assertFalse($party->hasAddress());
        self::assertNull($party->getPostalAddress());
        self::assertNull($party->getStreetLine());
        self::assertNull($party->getStreetName());
        self::assertNull($party->getBuildingNumber());
        self::assertNull($party->getCityLine());
        self::assertNull($party->getPostalCode());
        self::assertNull($party->getDisplayAddress());

        self::assertFalse($party->hasIdentifications());
        self::assertNull($party->getIdentifications());
        self::assertTrue($party->getIdentificationsOrEmpty()->isEmpty());
        self::assertNull($party->getCompanyNumber());
        self::assertNull($party->getVatId());

        self::assertSame([], $party->getExtra());
    }
}
