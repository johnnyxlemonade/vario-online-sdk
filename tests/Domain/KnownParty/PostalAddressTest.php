<?php declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\KnownParty;

use Lemonade\Vario\Domain\KnownParty\PostalAddress;
use PHPUnit\Framework\TestCase;

final class PostalAddressTest extends TestCase
{
    public function test_street_line_returns_normalized_street(): void
    {
        $address = new PostalAddress(
            street: 'Main Street 10',
            city: 'Prague',
            postalCode: '11000',
            countryIso: 'CZ',
            formatted: null
        );

        self::assertSame('Main Street 10', $address->getStreetLine());
    }

    public function test_city_line_is_composed_from_postal_code_and_city(): void
    {
        $address = new PostalAddress(
            street: 'Main',
            city: 'Prague',
            postalCode: '11000',
            countryIso: 'CZ',
            formatted: null
        );

        self::assertSame('11000 Prague', $address->getCityLine());
    }

    public function test_display_address_is_composed_from_parts(): void
    {
        $address = new PostalAddress(
            street: 'Main 10',
            city: 'Prague',
            postalCode: '11000',
            countryIso: 'CZ',
            formatted: null
        );

        self::assertSame(
            'Main 10, 11000 Prague, CZ',
            $address->getDisplayAddress()
        );
    }

    public function test_display_address_uses_formatted_value_when_available(): void
    {
        $address = new PostalAddress(
            street: '',
            city: '',
            postalCode: '',
            countryIso: '',
            formatted: "Main 10\n11000 Prague"
        );

        self::assertSame(
            'Main 10, 11000 Prague',
            $address->getDisplayAddress()
        );
    }

    public function test_json_serialization_returns_expected_structure(): void
    {
        $address = new PostalAddress(
            street: 'Main 10',
            city: 'Prague',
            postalCode: '11000',
            countryIso: 'CZ',
            formatted: null
        );

        $data = $address->jsonSerialize();

        self::assertSame('Main 10', $data['street']);
        self::assertSame('Main 10', $data['streetLine']);
        self::assertSame('Prague', $data['city']);
        self::assertSame('11000 Prague', $data['cityLine']);
        self::assertSame('11000', $data['postalCode']);
        self::assertSame('CZ', $data['countryIso']);
    }

    public function test_string_cast_returns_display_address(): void
    {
        $address = new PostalAddress(
            street: 'Main 10',
            city: 'Prague',
            postalCode: '11000',
            countryIso: 'CZ',
            formatted: null
        );

        self::assertSame(
            'Main 10, 11000 Prague, CZ',
            (string)$address
        );
    }

    public function test_display_address_handles_missing_values(): void
    {
        $address = new PostalAddress(
            street: '',
            city: '',
            postalCode: '27601',
            countryIso: '',
            formatted: null
        );

        self::assertSame('27601', $address->getDisplayAddress());
    }

    public function test_whitespace_is_normalized(): void
    {
        $address = new PostalAddress(
            street: "Main   Street\t10",
            city: 'Prague',
            postalCode: '11000',
            countryIso: 'CZ',
            formatted: null
        );

        self::assertSame(
            'Main Street 10',
            $address->getStreetLine()
        );
    }
}
