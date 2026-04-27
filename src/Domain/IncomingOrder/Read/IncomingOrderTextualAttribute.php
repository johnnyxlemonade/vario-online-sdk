<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Read;

use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTextualAttributeKind;

/**
 * Class IncomingOrderTextualAttribute
 *
 * Immutable domain read model representing a textual attribute
 * returned inside IncomingOrder line item payload.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderTextualAttribute
{
    /**
     * @param array<string,mixed> $extra Additional unmapped API fields.
     */
    public function __construct(
        private readonly IncomingOrderTextualAttributeKind $attributeKind,
        private readonly ?string $name = null,
        private readonly ?string $value = null,
        private readonly ?string $langId = null,
        private readonly array $extra = [],
    ) {}

    public function getAttributeKind(): IncomingOrderTextualAttributeKind
    {
        return $this->attributeKind;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getLangId(): ?string
    {
        return $this->langId;
    }

    /**
     * @return array<string,mixed>
     */
    public function getExtra(): array
    {
        return $this->extra;
    }
}
