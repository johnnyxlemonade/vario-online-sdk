<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\Write;

use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTextualAttributeKind;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;

final class DocumentAdditionalAttributeInput
{
    public function __construct(
        private readonly string $name,
        private readonly string $value,
        private readonly DocumentTextualAttributeKind $attributeKind = DocumentTextualAttributeKind::ExtendedID,
        private readonly ?string $langId = null,
        private readonly ?string $unitCode = null,
        private readonly ?DocumentUnitOfMeasureScheme $scheme = null,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getAttributeKind(): DocumentTextualAttributeKind
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

    public function getScheme(): ?DocumentUnitOfMeasureScheme
    {
        return $this->scheme;
    }
}
