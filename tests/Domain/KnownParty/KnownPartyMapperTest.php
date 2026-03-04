<?php declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\KnownParty;

use Lemonade\Vario\Domain\KnownParty\DefaultKnownPartyFactory;
use Lemonade\Vario\Domain\KnownParty\IdentificationCollection;
use Lemonade\Vario\Domain\KnownParty\IdentificationScheme;
use Lemonade\Vario\Domain\KnownParty\KnownPartyMapper;
use PHPUnit\Framework\TestCase;

final class KnownPartyMapperTest extends TestCase
{
    private KnownPartyMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new KnownPartyMapper(
            new DefaultKnownPartyFactory()
        );
    }

    public function test_maps_basic_known_party(): void
    {
        $payload = [
            'UUID' => 'abc-123',
            'Name' => 'Test Company',
        ];

        $party = $this->mapper->map($payload);

        self::assertSame('abc-123', $party->getUuid());
        self::assertSame('Test Company', $party->getName());
    }

    public function test_uses_contact_person_when_name_missing(): void
    {
        $payload = [
            'UUID' => 'abc',
            'ContactPerson' => 'John Doe',
        ];

        $party = $this->mapper->map($payload);

        self::assertSame('John Doe', $party->getName());
    }

    public function test_falls_back_to_id_when_name_missing(): void
    {
        $payload = [
            'UUID' => 'abc',
            'ID' => 'customer123',
        ];

        $party = $this->mapper->map($payload);

        self::assertSame('customer123', $party->getName());
    }

    public function test_maps_email_and_phone(): void
    {
        $payload = [
            'UUID' => 'abc',
            'ElectronicMail' => 'test@example.com',
            'Telephone' => '123456789',
        ];

        $party = $this->mapper->map($payload);

        self::assertSame('test@example.com', $party->getEmail());
        self::assertSame('123456789', $party->getTelephone());
    }

    public function test_maps_address(): void
    {
        $payload = [
            'UUID' => 'abc',
            'PostalAddress' => [
                'StreetName' => 'Main',
                'BuildingNumber' => '123',
                'CityName' => 'Prague',
                'PostalZone' => '11000',
                'CountryIso' => 'CZ',
            ]
        ];

        $party = $this->mapper->map($payload);
        $address = $party->getPostalAddress();

        self::assertNotNull($address);
        self::assertSame('Main 123', $address->getStreet());
        self::assertSame('Prague', $address->getCity());
        self::assertSame('11000', $address->getPostalCode());
    }

    public function test_returns_null_address_when_empty(): void
    {
        $payload = [
            'UUID' => 'abc',
            'PostalAddress' => []
        ];

        $party = $this->mapper->map($payload);

        self::assertNull($party->getPostalAddress());
    }

    public function test_maps_identifications(): void
    {
        $payload = [
            'UUID' => 'abc',
            'Identifications' => [
                [
                    'Scheme' => IdentificationScheme::VAT->value,
                    'ID' => 'CZ12345678',
                    'OriginCountry' => 'CZ',
                ]
            ]
        ];

        $party = $this->mapper->map($payload);
        $ids = $party->getIdentificationsOrEmpty();

        self::assertInstanceOf(IdentificationCollection::class, $ids);
        self::assertSame('CZ12345678', $ids->getVatIdValue());
    }

    public function test_ignores_invalid_identifications(): void
    {
        $payload = [
            'UUID' => 'abc',
            'Identifications' => [
                [
                    'Scheme' => 999,
                    'ID' => 'invalid',
                ]
            ]
        ];

        $party = $this->mapper->map($payload);

        self::assertTrue(
            $party->getIdentificationsOrEmpty()->isEmpty()
        );
    }

    public function test_extracts_company_number(): void
    {
        $payload = [
            'UUID' => 'abc',
            'Identifications' => [
                [
                    'Scheme' => IdentificationScheme::UIN->value,
                    'ID' => '12345678',
                ]
            ]
        ];

        $party = $this->mapper->map($payload);

        self::assertSame('12345678', $party->getCompanyNumber());
    }

    public function test_extracts_vat_id(): void
    {
        $payload = [
            'UUID' => 'abc',
            'Identifications' => [
                [
                    'Scheme' => IdentificationScheme::VAT->value,
                    'ID' => 'CZ12345678',
                ]
            ]
        ];

        $party = $this->mapper->map($payload);

        self::assertSame('CZ12345678', $party->getVatId());
    }

    public function test_throws_when_uuid_missing(): void
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->mapper->map([
            'Name' => 'Company'
        ]);
    }

    public function test_trims_address_values(): void
    {
        $payload = [
            'UUID' => 'abc',
            'PostalAddress' => [
                'StreetName' => ' Main ',
                'BuildingNumber' => ' 10 ',
                'CityName' => ' Prague ',
            ]
        ];

        $party = $this->mapper->map($payload);
        $address = $party->getPostalAddress();

        self::assertNotNull($address);

        self::assertSame('Main 10', $address->getStreet());
        self::assertSame('Prague', $address->getCity());
    }

    public function test_identification_without_id_is_ignored(): void
    {
        $payload = [
            'UUID' => 'abc',
            'Identifications' => [
                [
                    'Scheme' => IdentificationScheme::VAT->value
                ]
            ]
        ];

        $party = $this->mapper->map($payload);

        self::assertTrue(
            $party->getIdentificationsOrEmpty()->isEmpty()
        );
    }

    public function test_extra_fields_are_preserved(): void
    {
        $payload = [
            'UUID' => 'abc',
            'Name' => 'Test',
            'CustomField' => 'value'
        ];

        $party = $this->mapper->map($payload);

        self::assertSame(
            'value',
            $party->getExtra()['CustomField']
        );
    }

    public function test_ignores_invalid_address_type(): void
    {
        $payload = [
            'UUID' => 'abc',
            'PostalAddress' => 'invalid'
        ];

        $party = $this->mapper->map($payload);

        self::assertNull($party->getPostalAddress());
    }
}
