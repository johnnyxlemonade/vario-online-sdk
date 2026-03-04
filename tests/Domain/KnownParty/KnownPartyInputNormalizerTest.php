<?php declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\KnownParty;

use Lemonade\Vario\Domain\KnownParty\Identification;
use Lemonade\Vario\Domain\KnownParty\IdentificationScheme;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInputNormalizer;
use Lemonade\Vario\Domain\KnownParty\PostalAddress;
use PHPUnit\Framework\TestCase;

final class KnownPartyInputNormalizerTest extends TestCase
{
    public function test_normalizes_basic_payload(): void
    {
        $input = new KnownPartyInput('Test Company');

        $payload = (new KnownPartyInputNormalizer())->normalize($input);

        /** @var array{Name:string} $payload */
        self::assertSame('Test Company', $payload['Name']);
    }

    public function test_optional_fields_are_included_when_present(): void
    {
        $input = (new KnownPartyInput('Test Company'))
            ->withContactPerson('John Doe')
            ->withEmail('john@example.com')
            ->withTelephone('123456');

        $payload = (new KnownPartyInputNormalizer())->normalize($input);

        /** @var array{
         *  Name:string,
         *  ContactPerson:string,
         *  ElectronicMail:string,
         *  Telephone:string
         * } $payload */

        self::assertSame('John Doe', $payload['ContactPerson']);
        self::assertSame('john@example.com', $payload['ElectronicMail']);
        self::assertSame('123456', $payload['Telephone']);
    }

    public function test_address_is_normalized(): void
    {
        $address = new PostalAddress(
            street: 'Main 10',
            city: 'Prague',
            postalCode: '11000',
            countryIso: 'CZ',
            formatted: null
        );

        $input = (new KnownPartyInput('Test Company'))
            ->withAddress($address);

        $payload = (new KnownPartyInputNormalizer())->normalize($input);

        self::assertArrayHasKey('PostalAddress', $payload);

        assert(isset($payload['PostalAddress']));
        /** @var array{
         *  StreetName:string,
         *  CityName:string,
         *  PostalZone:string,
         *  CountryIso:string,
         *  Formated?:string
         * } $addr */
        $addr = $payload['PostalAddress'];

        self::assertSame('Main 10', $addr['StreetName']);
        self::assertSame('Prague', $addr['CityName']);
        self::assertSame('11000', $addr['PostalZone']);
        self::assertSame('CZ', $addr['CountryIso']);
    }

    public function test_identifications_are_mapped(): void
    {
        $input = (new KnownPartyInput('Test Company'))
            ->addIdentification(
                new Identification(
                    IdentificationScheme::VAT,
                    'CZ12345678',
                    'CZ'
                )
            );

        $payload = (new KnownPartyInputNormalizer())->normalize($input);

        self::assertArrayHasKey('Identifications', $payload);

        assert(isset($payload['Identifications']));
        /** @var list<array{Scheme:int,ID:string,OriginCountry?:string}> $ids */
        $ids = $payload['Identifications'];

        self::assertCount(1, $ids);

        self::assertSame(
            IdentificationScheme::VAT->value,
            $ids[0]['Scheme']
        );

        self::assertSame('CZ12345678', $ids[0]['ID']);
    }

    public function test_empty_identifications_are_not_sent(): void
    {
        $input = new KnownPartyInput('Test Company');

        $payload = (new KnownPartyInputNormalizer())->normalize($input);

        self::assertArrayNotHasKey('Identifications', $payload);
    }
}
