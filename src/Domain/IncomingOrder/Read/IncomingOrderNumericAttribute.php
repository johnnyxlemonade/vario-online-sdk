<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Read;

use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderNumericAttributeKind;

/**
 * Class IncomingOrderNumericAttribute
 *
 * Immutable domain read model representing a numeric attribute
 * returned inside IncomingOrder line item payload.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderNumericAttribute
{
    /**
     * @param array<string,mixed> $extra Additional unmapped API fields.
     */
    public function __construct(
        private readonly IncomingOrderNumericAttributeKind $attributeKind,
        private readonly ?string $name = null,
        private readonly ?float $value = null,
        private readonly ?string $unitCode = null,
        private readonly array $extra = [],
    ) {}

    public function getAttributeKind(): IncomingOrderNumericAttributeKind
    {
        return $this->attributeKind;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function getUnitCode(): ?string
    {
        return $this->unitCode;
    }

    /**
     * @return array<string,mixed>
     */
    public function getExtra(): array
    {
        return $this->extra;
    }
}
