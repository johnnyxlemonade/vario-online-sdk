<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Write;

use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTextualAttributeKind;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderUnitOfMeasureScheme;

/**
 * Class IncomingOrderAdditionalAttributeInput
 *
 * Additional textual attribute for IncomingOrder line item payloads.
 *
 * This object is primarily used for product variants and other
 * line-level textual metadata supported by the Vario IncomingOrder API.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderAdditionalAttributeInput
{
    public function __construct(
        private readonly string $name,
        private readonly string $value,
        private readonly IncomingOrderTextualAttributeKind $attributeKind = IncomingOrderTextualAttributeKind::ExtendedID,
        private readonly ?string $langId = null,
        private readonly ?string $unitCode = null,
        private readonly ?IncomingOrderUnitOfMeasureScheme $scheme = null,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getAttributeKind(): IncomingOrderTextualAttributeKind
    {
        return $this->attributeKind;
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
}
