<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\Write;

use InvalidArgumentException;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTextualAttributeKind;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;

final class DocumentAdditionalAttributeInput
{
    public function __construct(
        private readonly string $name,
        private readonly DocumentAdditionalAttributeValueInput $value,
        private readonly DocumentTextualAttributeKind $attributeKind = DocumentTextualAttributeKind::ExtendedID,
    ) {
        if ($name === '') {
            throw new InvalidArgumentException('Additional attribute name must not be empty.');
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value->getValue();
    }

    public function getAttributeKind(): DocumentTextualAttributeKind
    {
        return $this->attributeKind;
    }

    public function getLangId(): ?string
    {
        return $this->value->getLangId();
    }

    public function getUnitCode(): ?string
    {
        return $this->value->getUnitCode();
    }

    public function getScheme(): ?DocumentUnitOfMeasureScheme
    {
        return $this->value->getScheme();
    }
}
