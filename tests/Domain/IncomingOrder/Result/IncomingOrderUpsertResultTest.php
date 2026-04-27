<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Result;

use DateTimeImmutable;
use Lemonade\Vario\Domain\IncomingOrder\Result\IncomingOrderUpsertResult;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

final class IncomingOrderUpsertResultTest extends TestCase
{
    public function test_it_exposes_constructor_values(): void
    {
        $issueDate = new DateTimeImmutable('2024-04-02T00:00:00+02:00');
        $receiveDate = new DateTimeImmutable('2024-04-03T00:00:00+02:00');

        $result = new IncomingOrderUpsertResult(
            uuid: 'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
            issuerObjectId: '1001',
            recipientObjectId: 'ESHOP-1001',
            issueDate: $issueDate,
            receiveDate: $receiveDate,
        );

        self::assertSame('e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8', $result->getUuid());
        self::assertSame('1001', $result->getIssuerObjectId());
        self::assertSame('ESHOP-1001', $result->getRecipientObjectId());
        self::assertSame($issueDate, $result->getIssueDate());
        self::assertSame($receiveDate, $result->getReceiveDate());
        self::assertTrue($result->hasIssuerObjectId());
        self::assertTrue($result->hasRecipientObjectId());
    }

    public function test_it_supports_nullable_optional_fields(): void
    {
        $result = new IncomingOrderUpsertResult(
            uuid: 'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
        );

        self::assertSame('e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8', $result->getUuid());
        self::assertNull($result->getIssuerObjectId());
        self::assertNull($result->getRecipientObjectId());
        self::assertNull($result->getIssueDate());
        self::assertNull($result->getReceiveDate());
        self::assertFalse($result->hasIssuerObjectId());
        self::assertFalse($result->hasRecipientObjectId());
    }

    public function test_empty_string_object_ids_are_not_considered_present(): void
    {
        $result = new IncomingOrderUpsertResult(
            uuid: 'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
            issuerObjectId: '',
            recipientObjectId: '',
        );

        self::assertSame('', $result->getIssuerObjectId());
        self::assertSame('', $result->getRecipientObjectId());
        self::assertFalse($result->hasIssuerObjectId());
        self::assertFalse($result->hasRecipientObjectId());
    }

    public function test_from_array_maps_valid_payload(): void
    {
        $result = IncomingOrderUpsertResult::fromArray([
            'UUID' => 'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
            'IssuerObjectID' => '1001',
            'RecipientObjectID' => 'ESHOP-1001',
            'IssueDate' => '2024-04-02T00:00:00+02:00',
            'ReceiveDate' => '2024-04-03T00:00:00+02:00',
        ]);

        self::assertSame('e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8', $result->getUuid());
        self::assertSame('1001', $result->getIssuerObjectId());
        self::assertSame('ESHOP-1001', $result->getRecipientObjectId());
        self::assertSame('2024-04-02T00:00:00+02:00', $result->getIssueDate()?->format(DATE_ATOM));
        self::assertSame('2024-04-03T00:00:00+02:00', $result->getReceiveDate()?->format(DATE_ATOM));
    }

    public function test_from_array_maps_non_string_object_ids_to_null(): void
    {
        $result = IncomingOrderUpsertResult::fromArray([
            'UUID' => 'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
            'IssuerObjectID' => 1001,
            'RecipientObjectID' => false,
        ]);

        self::assertNull($result->getIssuerObjectId());
        self::assertNull($result->getRecipientObjectId());
        self::assertFalse($result->hasIssuerObjectId());
        self::assertFalse($result->hasRecipientObjectId());
    }

    public function test_from_array_allows_null_or_empty_dates(): void
    {
        $result = IncomingOrderUpsertResult::fromArray([
            'UUID' => 'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
            'IssueDate' => null,
            'ReceiveDate' => '',
        ]);

        self::assertNull($result->getIssueDate());
        self::assertNull($result->getReceiveDate());
    }

    public function test_from_array_throws_when_uuid_is_missing(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IncomingOrderUpsertResult payload missing valid UUID.');

        IncomingOrderUpsertResult::fromArray([]);
    }

    public function test_from_array_throws_when_uuid_is_empty(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IncomingOrderUpsertResult payload missing valid UUID.');

        IncomingOrderUpsertResult::fromArray([
            'UUID' => '',
        ]);
    }

    public function test_from_array_throws_when_issue_date_has_invalid_type(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage(
            'IncomingOrderUpsertResult field "IssueDate" must be a valid datetime string or null.'
        );

        IncomingOrderUpsertResult::fromArray([
            'UUID' => 'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
            'IssueDate' => 123,
        ]);
    }

    public function test_from_array_throws_when_receive_date_has_invalid_value(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage(
            'IncomingOrderUpsertResult field "ReceiveDate" contains invalid datetime value "not-a-date".'
        );

        IncomingOrderUpsertResult::fromArray([
            'UUID' => 'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
            'ReceiveDate' => 'not-a-date',
        ]);
    }

    public function test_to_array_returns_expected_shape(): void
    {
        $result = new IncomingOrderUpsertResult(
            uuid: 'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
            issuerObjectId: '1001',
            recipientObjectId: 'ESHOP-1001',
            issueDate: new DateTimeImmutable('2024-04-02T00:00:00+02:00'),
            receiveDate: new DateTimeImmutable('2024-04-03T00:00:00+02:00'),
        );

        self::assertSame([
            'uuid' => 'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
            'issuerObjectId' => '1001',
            'recipientObjectId' => 'ESHOP-1001',
            'issueDate' => '2024-04-02T00:00:00+02:00',
            'receiveDate' => '2024-04-03T00:00:00+02:00',
        ], $result->toArray());
    }
}
