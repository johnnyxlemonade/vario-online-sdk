<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\KnownParty;

use Lemonade\Vario\Domain\KnownParty\KnownPartyUpsertResult;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

final class KnownPartyUpsertResultTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $result = new KnownPartyUpsertResult(
            uuid: 'abc-123',
            recipientObjectId: '42'
        );

        self::assertSame('abc-123', $result->getUuid());
        self::assertSame('42', $result->getRecipientObjectId());
        self::assertTrue($result->hasRecipientObjectId());
    }

    public function testHasRecipientObjectIdFalseWhenNull(): void
    {
        $result = new KnownPartyUpsertResult(
            uuid: 'abc-123',
            recipientObjectId: null
        );

        self::assertFalse($result->hasRecipientObjectId());
    }

    public function testHasRecipientObjectIdFalseWhenEmptyString(): void
    {
        $result = new KnownPartyUpsertResult(
            uuid: 'abc-123',
            recipientObjectId: ''
        );

        self::assertFalse($result->hasRecipientObjectId());
    }

    public function testFromArray(): void
    {
        $result = KnownPartyUpsertResult::fromArray([
            'UUID' => 'abc-123',
            'RecipientObjectID' => '55',
        ]);

        self::assertSame('abc-123', $result->getUuid());
        self::assertSame('55', $result->getRecipientObjectId());
    }

    public function testFromArrayWithoutObjectId(): void
    {
        $result = KnownPartyUpsertResult::fromArray([
            'UUID' => 'abc-123',
        ]);

        self::assertSame('abc-123', $result->getUuid());
        self::assertNull($result->getRecipientObjectId());
    }

    public function testFromArrayThrowsWhenUuidMissing(): void
    {
        $this->expectException(UnexpectedValueException::class);

        KnownPartyUpsertResult::fromArray([
            'RecipientObjectID' => '55',
        ]);
    }

    public function testToArray(): void
    {
        $result = new KnownPartyUpsertResult(
            uuid: 'abc-123',
            recipientObjectId: '55'
        );

        self::assertSame([
            'uuid' => 'abc-123',
            'recipientObjectId' => '55',
        ], $result->toArray());
    }
}
