<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\KnownParty;

use UnexpectedValueException;

/**
 * Class KnownPartyUpsertResult
 *
 * Domain result model representing the confirmation returned by the
 * Vario API after a KnownParty upsert operation.
 *
 * The API responds with a minimal payload containing the UUID of the
 * processed entity and optionally an internal object identifier
 * (`RecipientObjectID`).
 *
 * Instances of this class are created from the raw API response using
 * the `fromArray()` factory method.
 *
 * This object is typically returned by:
 *
 *     KnownPartyApi::upsert()
 *
 * and allows integrations to safely access identifiers of the created
 * or updated contact.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrák
 * @license     MIT
 * @since       1.0
 */
final class KnownPartyUpsertResult
{
    public function __construct(
        private readonly string $uuid,
        private readonly ?string $recipientObjectId = null
    ) {}

    public function getUuid(): string
    {
        return $this->uuid;
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

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $uuid = $data['UUID'] ?? null;

        if (!is_string($uuid) || $uuid === '') {
            throw new UnexpectedValueException(
                'KnownPartyUpsertResult payload missing valid UUID.'
            );
        }

        $objectId = $data['RecipientObjectID'] ?? null;

        return new self(
            uuid: $uuid,
            recipientObjectId: is_string($objectId) ? $objectId : null
        );
    }

    /**
     * Debug / serialization helper.
     *
     * @return array{
     *     uuid:string,
     *     recipientObjectId:?string
     * }
     */
    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'recipientObjectId' => $this->recipientObjectId,
        ];
    }
}
