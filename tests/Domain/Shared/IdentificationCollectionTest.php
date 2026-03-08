<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Shared;

use Lemonade\Vario\Domain\Shared\Identification;
use Lemonade\Vario\Domain\Shared\IdentificationCollection;
use Lemonade\Vario\Domain\Shared\IdentificationScheme;
use PHPUnit\Framework\TestCase;

final class IdentificationCollectionTest extends TestCase
{
    public function testIteratorAndCount(): void
    {
        $i1 = new Identification(IdentificationScheme::UIN, '12345678', 'CZ');
        $i2 = new Identification(IdentificationScheme::VAT, 'CZ12345678', 'CZ');

        $collection = new IdentificationCollection([$i1, $i2]);

        $items = iterator_to_array($collection);

        self::assertCount(2, $items);
        self::assertSame(2, $collection->count());
    }

    public function testIsEmpty(): void
    {
        $collection = new IdentificationCollection([]);

        self::assertTrue($collection->isEmpty());
    }

    public function testFirstByScheme(): void
    {
        $uin = new Identification(IdentificationScheme::UIN, '12345678', 'CZ');
        $vat = new Identification(IdentificationScheme::VAT, 'CZ12345678', 'CZ');

        $collection = new IdentificationCollection([$uin, $vat]);

        $found = $collection->firstByScheme(IdentificationScheme::VAT);

        self::assertSame($vat, $found);
    }

    public function testFirstBySchemeReturnsNull(): void
    {
        $uin = new Identification(IdentificationScheme::UIN, '12345678', 'CZ');

        $collection = new IdentificationCollection([$uin]);

        self::assertNull(
            $collection->firstByScheme(IdentificationScheme::VAT)
        );
    }

    public function testGetCompanyNumber(): void
    {
        $uin = new Identification(IdentificationScheme::UIN, '12345678', 'CZ');
        $vat = new Identification(IdentificationScheme::VAT, 'CZ12345678', 'CZ');

        $collection = new IdentificationCollection([$uin, $vat]);

        self::assertSame($uin, $collection->getCompanyNumber());
    }

    public function testGetCompanyNumberIgnoresVatPrefixed(): void
    {
        $uin = new Identification(IdentificationScheme::UIN, 'CZ12345678', 'CZ');

        $collection = new IdentificationCollection([$uin]);

        self::assertNull($collection->getCompanyNumber());
    }

    public function testGetVatId(): void
    {
        $vat = new Identification(IdentificationScheme::VAT, 'CZ12345678', 'CZ');

        $collection = new IdentificationCollection([$vat]);

        self::assertSame($vat, $collection->getVatId());
    }

    public function testGetVatIdFallbackFromUin(): void
    {
        $uin = new Identification(IdentificationScheme::UIN, 'CZ12345678', 'CZ');

        $collection = new IdentificationCollection([$uin]);

        self::assertSame($uin, $collection->getVatId());
    }

    public function testGetCompanyNumberValue(): void
    {
        $uin = new Identification(IdentificationScheme::UIN, '12345678', 'CZ');

        $collection = new IdentificationCollection([$uin]);

        self::assertSame('12345678', $collection->getCompanyNumberValue());
    }

    public function testGetVatIdValue(): void
    {
        $vat = new Identification(IdentificationScheme::VAT, 'CZ12345678', 'CZ');

        $collection = new IdentificationCollection([$vat]);

        self::assertSame('CZ12345678', $collection->getVatIdValue());
    }

    public function testToArray(): void
    {
        $id = new Identification(
            IdentificationScheme::VAT,
            'CZ12345678',
            'CZ'
        );

        $collection = new IdentificationCollection([$id]);

        $data = $collection->toArray();

        self::assertSame('VAT', $data[0]['scheme']);
        self::assertSame('CZ12345678', $data[0]['id']);
        self::assertSame('CZ', $data[0]['originCountry']);
    }

    public function testAllReturnsItems(): void
    {
        $i1 = new Identification(IdentificationScheme::UIN, '12345678', 'CZ');
        $i2 = new Identification(IdentificationScheme::VAT, 'CZ12345678', 'CZ');

        $collection = new IdentificationCollection([$i1, $i2]);

        $all = $collection->all();

        self::assertCount(2, $all);
        self::assertSame($i1, $all[0]);
        self::assertSame($i2, $all[1]);
    }
}
