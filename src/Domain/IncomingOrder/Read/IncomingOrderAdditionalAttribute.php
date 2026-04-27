<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Read;

use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTextualAttributeKind;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderUnitOfMeasureScheme;

/**
 * Class IncomingOrderAdditionalAttribute
 *
 * Immutable domain read model representing an additional textual
 * attribute returned inside IncomingOrder line item payload.
 *
 * This structure is similar to the write-side additional attribute
 * input, but is kept separate to avoid mixing read and write models.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderAdditionalAttribute
{
    /**
     * @param array<string,mixed> $extra Additional unmapped API fields.
     */
    public function __construct(
        private readonly IncomingOrderTextualAttributeKind $attributeKind,
        private readonly string $name,
        private readonly string $value,
        private readonly ?string $langId = null,
        private readonly ?string $unitCode = null,
        private readonly ?IncomingOrderUnitOfMeasureScheme $scheme = null,
        private readonly array $extra = [],
    ) {}

    public function getAttributeKind(): IncomingOrderTextualAttributeKind
    {
        return $this->attributeKind;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getLangId(): ?string
    {
        return $this->langId;
    }

    public function getUnitCode(): ?string
    {
        return $this->unitCode;
    }

    public function getScheme(): ?IncomingOrderUnitOfMeasureScheme
    {
        return $this->scheme;
    }

    /**
     * @return array<string,mixed>
     */
    public function getExtra(): array
    {
        return $this->extra;
    }
}
