<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\KnownParty;

use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\KnownParty\KnownPartyKind;
use Lemonade\Vario\Domain\Shared\Identification;
use Lemonade\Vario\Domain\Shared\IdentificationScheme;
use Lemonade\Vario\Domain\Shared\PostalAddress;
use PHPUnit\Framework\TestCase;

final class KnownPartyInputTest extends TestCase
{
    public function testConstructorAndGetName(): void
    {
        $input = new KnownPartyInput('Test Company');

        self::assertSame('Test Company', $input->getName());
    }

    public function testFluentModifiers(): void
    {
        $address = new PostalAddress(
            street: 'Main',
            city: 'Prague',
            postalCode: '11000',
            countryIso: 'CZ',
            buildingNumber: '10'
        );

        $input = (new KnownPartyInput('Company'))
            ->withUuid('uuid-1')
            ->withId('C001')
            ->withKind(KnownPartyKind::Organization)
            ->withContactPerson('John Doe')
            ->withEmail('john@example.com')
            ->withTelephone('123456')
            ->withAddress($address);

        self::assertSame('uuid-1', $input->getUuid());
        self::assertSame('C001', $input->getId());
        self::assertSame(KnownPartyKind::Organization, $input->getKind());
        self::assertSame('John Doe', $input->getContactPerson());
        self::assertSame('john@example.com', $input->getEmail());
        self::assertSame('123456', $input->getTelephone());
        self::assertSame($address, $input->getAddress());
    }

    public function testWithIdentifications(): void
    {
        $id = new Identification(
            scheme: IdentificationScheme::VAT,
            id: 'CZ12345678',
            originCountry: 'CZ'
        );

        $input = (new KnownPartyInput('Company'))
            ->withIdentifications([$id]);

        self::assertCount(1, $input->getIdentifications());
        self::assertSame($id, $input->getIdentifications()[0]);
    }

    public function testAddIdentification(): void
    {
        $id = new Identification(
            scheme: IdentificationScheme::VAT,
            id: 'CZ12345678',
            originCountry: 'CZ'
        );

        $input = (new KnownPartyInput('Company'))
            ->addIdentification($id);

        self::assertCount(1, $input->getIdentifications());
        self::assertSame($id, $input->getIdentifications()[0]);
    }

    public function testFluentReturnsSameInstance(): void
    {
        $input = new KnownPartyInput('Company');

        $returned = $input->withEmail('test@example.com');

        self::assertSame($input, $returned);
    }
}
