<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\KnownParty;

use Lemonade\Vario\Domain\Shared\Identification;
use Lemonade\Vario\Domain\Shared\IdentificationCollection;
use Lemonade\Vario\Domain\Shared\IdentificationScheme;
use PHPUnit\Framework\TestCase;

final class IdentificationCollectionTest extends TestCase
{
    public function test_company_number_detection(): void
    {
        $collection = new IdentificationCollection([
            new Identification(
                IdentificationScheme::UIN,
                '12345678',
                'CZ'
            ),
        ]);

        self::assertSame('12345678', $collection->getCompanyNumberValue());
    }

    public function test_company_number_ignores_cz_prefixed_values(): void
    {
        $collection = new IdentificationCollection([
            new Identification(
                IdentificationScheme::UIN,
                'CZ12345678',
                'CZ'
            ),
        ]);

        self::assertNull($collection->getCompanyNumberValue());
    }

    public function test_vat_detection(): void
    {
        $collection = new IdentificationCollection([
            new Identification(
                IdentificationScheme::VAT,
                'CZ12345678',
                'CZ'
            ),
        ]);

        self::assertSame('CZ12345678', $collection->getVatIdValue());
    }

    public function test_vat_fallback_to_cz_prefix(): void
    {
        $collection = new IdentificationCollection([
            new Identification(
                IdentificationScheme::UIN,
                'CZ12345678',
                'CZ'
            ),
        ]);

        self::assertSame('CZ12345678', $collection->getVatIdValue());
    }

    public function test_first_by_scheme(): void
    {
        $id = new Identification(
            IdentificationScheme::VAT,
            'CZ123',
            'CZ'
        );

        $collection = new IdentificationCollection([$id]);

        self::assertSame(
            $id,
            $collection->firstByScheme(IdentificationScheme::VAT)
        );
    }

    public function test_is_empty(): void
    {
        $collection = new IdentificationCollection([]);

        self::assertTrue($collection->isEmpty());
    }

    public function test_count(): void
    {
        $collection = new IdentificationCollection([
            new Identification(IdentificationScheme::VAT, 'CZ1', 'CZ'),
            new Identification(IdentificationScheme::UIN, '123', 'CZ'),
        ]);

        self::assertSame(2, $collection->count());
    }

    public function test_to_array_projection(): void
    {
        $collection = new IdentificationCollection([
            new Identification(
                IdentificationScheme::VAT,
                'CZ12345678',
                'CZ'
            ),
        ]);

        $array = $collection->toArray();

        self::assertSame('VAT', $array[0]['scheme']);
        self::assertSame('CZ12345678', $array[0]['id']);
        self::assertSame('CZ', $array[0]['originCountry']);
    }

    public function test_empty_collection_returns_null(): void
    {
        $collection = new IdentificationCollection([]);

        self::assertNull($collection->getCompanyNumberValue());
        self::assertNull($collection->getVatIdValue());
    }

    public function test_iterator_returns_identifications(): void
    {
        $collection = new IdentificationCollection([
            new Identification(IdentificationScheme::VAT, 'CZ123', 'CZ'),
        ]);

        foreach ($collection as $item) {
            self::assertInstanceOf(Identification::class, $item);
        }
    }
}
