<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\OutgoingQuotation\Result;

use DateTimeImmutable;
use Lemonade\Vario\Domain\OutgoingQuotation\Result\OutgoingQuotationUpsertResult;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

final class OutgoingQuotationUpsertResultTest extends TestCase
{
    public function test_from_array_maps_valid_payload(): void
    {
        $result = OutgoingQuotationUpsertResult::fromArray([
            'UUID' => 'c676048c-3789-4228-82b2-9ca6e7b952f7',
            'IssuerObjectID' => '1001',
            'RecipientObjectID' => 'ESHOP-1001',
            'IssueDate' => '2026-06-18T00:00:00+02:00',
            'ReceiveDate' => '2026-06-19T00:00:00+02:00',
        ]);

        self::assertSame([
            'uuid' => 'c676048c-3789-4228-82b2-9ca6e7b952f7',
            'issuerObjectId' => '1001',
            'recipientObjectId' => 'ESHOP-1001',
            'issueDate' => '2026-06-18T00:00:00+02:00',
            'receiveDate' => '2026-06-19T00:00:00+02:00',
        ], $result->toArray());
    }

    public function test_constructor_exposes_values(): void
    {
        $issueDate = new DateTimeImmutable('2026-06-18T00:00:00+02:00');
        $receiveDate = new DateTimeImmutable('2026-06-19T00:00:00+02:00');

        $result = new OutgoingQuotationUpsertResult(
            uuid: 'c676048c-3789-4228-82b2-9ca6e7b952f7',
            issuerObjectId: '1001',
            recipientObjectId: 'ESHOP-1001',
            issueDate: $issueDate,
            receiveDate: $receiveDate,
        );

        self::assertSame('c676048c-3789-4228-82b2-9ca6e7b952f7', $result->getUuid());
        self::assertSame('1001', $result->getIssuerObjectId());
        self::assertSame('ESHOP-1001', $result->getRecipientObjectId());
        self::assertSame($issueDate, $result->getIssueDate());
        self::assertSame($receiveDate, $result->getReceiveDate());
        self::assertTrue($result->hasIssuerObjectId());
        self::assertTrue($result->hasRecipientObjectId());
    }

    public function test_from_array_throws_when_uuid_is_missing(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('OutgoingQuotationUpsertResult payload missing valid UUID.');

        OutgoingQuotationUpsertResult::fromArray([]);
    }
}
