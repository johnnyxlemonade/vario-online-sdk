<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\KnownParty;

use Lemonade\Vario\Domain\KnownParty\DefaultKnownPartyFactory;
use Lemonade\Vario\Domain\KnownParty\IdentificationCollection;
use Lemonade\Vario\Domain\KnownParty\KnownPartyKind;
use Lemonade\Vario\Domain\KnownParty\PostalAddress;
use PHPUnit\Framework\TestCase;

final class DefaultKnownPartyFactoryTest extends TestCase
{
    public function test_creates_known_party_instance(): void
    {
        $factory = new DefaultKnownPartyFactory();

        $address = new PostalAddress(
            street: 'Main 10',
            city: 'Prague',
            postalCode: '11000',
            countryIso: 'CZ'
        );

        $identifications = new IdentificationCollection([]);

        $party = $factory->create(
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
}
