<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Result;

use DateTimeImmutable;
use Throwable;
use UnexpectedValueException;

/**
 * Class IncomingOrderUpsertResult
 *
 * Domain result model representing the confirmation returned by the
 * Vario API after an IncomingOrder upsert operation.
 *
 * The API responds with a minimal payload containing the UUID of the
 * processed order and optionally object identifiers and timestamps.
 *
 * Instances of this class are created from the raw API response using
 * the fromArray() factory method.
 *
 * This object is typically returned by:
 *
 *     IncomingOrderApi::upsertInputs()
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderUpsertResult
{
    public function __construct(
        private readonly string $uuid,
        private readonly ?string $issuerObjectId = null,
        private readonly ?string $recipientObjectId = null,
        private readonly ?DateTimeImmutable $issueDate = null,
        private readonly ?DateTimeImmutable $receiveDate = null,
    ) {}

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getIssuerObjectId(): ?string
    {
        return $this->issuerObjectId;
    }

    public function hasIssuerObjectId(): bool
    {
        return $this->issuerObjectId !== null
            && $this->issuerObjectId !== '';
    }

    public function getRecipientObjectId(): ?string
    {
        return $this->recipientObjectId;
    }

    public function hasRecipientObjectId(): bool
    {
        return $this->recipientObjectId !== null
            && $this->recipientObjectId !== '';
    }

    public function getIssueDate(): ?DateTimeImmutable
    {
        return $this->issueDate;
    }

    public function getReceiveDate(): ?DateTimeImmutable
    {
        return $this->receiveDate;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $uuid = $data['UUID'] ?? null;

        if (!is_string($uuid) || $uuid === '') {
            throw new UnexpectedValueException(
                'IncomingOrderUpsertResult payload missing valid UUID.',
            );
        }

        $issuerObjectId = $data['IssuerObjectID'] ?? null;
        $recipientObjectId = $data['RecipientObjectID'] ?? null;

        return new self(
            uuid: $uuid,
            issuerObjectId: is_string($issuerObjectId) ? $issuerObjectId : null,
            recipientObjectId: is_string($recipientObjectId) ? $recipientObjectId : null,
            issueDate: self::parseNullableDateTime($data['IssueDate'] ?? null, 'IssueDate'),
            receiveDate: self::parseNullableDateTime($data['ReceiveDate'] ?? null, 'ReceiveDate'),
        );
    }

    /**
     * Debug / serialization helper.
     *
     * @return array{
     *     uuid:string,
     *     issuerObjectId:string|null,
     *     recipientObjectId:string|null,
     *     issueDate:string|null,
     *     receiveDate:string|null
     * }
     */
    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'issuerObjectId' => $this->issuerObjectId,
            'recipientObjectId' => $this->recipientObjectId,
            'issueDate' => $this->issueDate?->format(DATE_ATOM),
            'receiveDate' => $this->receiveDate?->format(DATE_ATOM),
        ];
    }

    /**
     */
    private static function parseNullableDateTime(mixed $value, string $field): ?DateTimeImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException(sprintf(
                'IncomingOrderUpsertResult field "%s" must be a valid datetime string or null.',
                $field,
            ));
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Throwable $e) {
            throw new UnexpectedValueException(sprintf(
                'IncomingOrderUpsertResult field "%s" contains invalid datetime value "%s".',
                $field,
                $value,
            ), 0, $e);
        }
    }
}
