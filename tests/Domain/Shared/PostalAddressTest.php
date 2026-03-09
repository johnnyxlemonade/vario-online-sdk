<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Shared;

use Lemonade\Vario\Domain\Shared\PostalAddress;
use PHPUnit\Framework\TestCase;

final class PostalAddressTest extends TestCase
{
    public function testAtomicGetters(): void
    {
        $address = new PostalAddress(
            street: 'Main',
            buildingNumber: '10',
            city: 'Prague',
            postalCode: '11000',
            countryIso: 'CZ'
        );

        self::assertSame('Main', $address->getStreet());
        self::assertSame('10', $address->getBuildingNumber());
        self::assertSame('Prague', $address->getCity());
        self::assertSame('11000', $address->getPostalCode());
        self::assertSame('CZ', $address->getCountryIso());
    }

    public function testStreetLine(): void
    {
        $address = new PostalAddress('Main', '10');

        self::assertSame('Main 10', $address->getStreetLine());
    }

    public function testStreetLineReturnsNullWhenEmpty(): void
    {
        $address = new PostalAddress(null, null);

        self::assertNull($address->getStreetLine());
    }

    public function testCityLine(): void
    {
        $address = new PostalAddress(
            street: null,
            buildingNumber: null,
            city: 'Prague',
            postalCode: '11000'
        );

        self::assertSame('11000 Prague', $address->getCityLine());
    }

    public function testCityLineReturnsNullWhenEmpty(): void
    {
        $address = new PostalAddress();

        self::assertNull($address->getCityLine());
    }

    public function testDisplayAddress(): void
    {
        $address = new PostalAddress(
            street: 'Main',
            buildingNumber: '10',
            city: 'Prague',
            postalCode: '11000',
            countryIso: 'CZ'
        );

        self::assertSame(
            'Main 10, 11000 Prague, CZ',
            $address->getDisplayAddress()
        );
    }

    public function testDisplayAddressReturnsNullWhenEmpty(): void
    {
        $address = new PostalAddress();

        self::assertNull($address->getDisplayAddress());
    }

    public function testToArray(): void
    {
        $address = new PostalAddress(
            street: 'Main',
            buildingNumber: '10',
            city: 'Prague',
            postalCode: '11000',
            countryIso: 'CZ'
        );

        $data = $address->toArray();

        self::assertSame('Main', $data['street']);
        self::assertSame('10', $data['buildingNumber']);
        self::assertSame('Main 10', $data['streetLine']);
        self::assertSame('Prague', $data['city']);
        self::assertSame('11000 Prague', $data['cityLine']);
        self::assertSame('11000', $data['postalCode']);
        self::assertSame('CZ', $data['countryIso']);
        self::assertSame('Main 10, 11000 Prague, CZ', $data['display']);
    }

    public function testToString(): void
    {
        $address = new PostalAddress(
            street: 'Main',
            buildingNumber: '10',
            city: 'Prague',
            postalCode: '11000'
        );

        self::assertSame('Main 10, 11000 Prague', (string) $address);
    }

    public function testJsonSerialize(): void
    {
        $address = new PostalAddress(
            street: 'Main',
            buildingNumber: '10',
            city: 'Prague',
            postalCode: '11000'
        );

        $json = $address->jsonSerialize();

        self::assertSame('Main 10', $json['streetLine']);
        self::assertSame('11000 Prague', $json['cityLine']);
    }

    public function testWhitespaceNormalization(): void
    {
        $address = new PostalAddress(
            street: "  Main\tStreet  ",
            buildingNumber: '  10 ',
            city: '  Prague ',
            postalCode: ' 11000 '
        );

        self::assertSame('Main Street 10', $address->getStreetLine());
        self::assertSame('11000 Prague', $address->getCityLine());
    }
}
