<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Read;

use DateTimeImmutable;

/**
 * Class IncomingOrderDeliveryDetail
 *
 * Immutable domain read model representing delivery detail
 * returned by the IncomingOrder API.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderDeliveryDetail
{
    /**
     * @param list<string> $optionCodes
     * @param array<string,mixed> $extra Additional unmapped API fields.
     */
    public function __construct(
        private readonly array $optionCodes = [],
        private readonly ?DateTimeImmutable $requestedDeliveryDate = null,
        private readonly array $extra = [],
    ) {}

    /**
     * @return list<string>
     */
    public function getOptionCodes(): array
    {
        return $this->optionCodes;
    }

    public function hasOptionCodes(): bool
    {
        return $this->optionCodes !== [];
    }

    public function getRequestedDeliveryDate(): ?DateTimeImmutable
    {
        return $this->requestedDeliveryDate;
    }

    /**
     * @return array<string,mixed>
     */
    public function getExtra(): array
    {
        return $this->extra;
    }
}
