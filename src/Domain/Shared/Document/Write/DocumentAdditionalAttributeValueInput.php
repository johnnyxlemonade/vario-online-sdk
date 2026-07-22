<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\Write;

use InvalidArgumentException;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;

final class DocumentAdditionalAttributeValueInput
{
    public function __construct(
        private readonly string $value,
        private readonly ?string $langId = null,
        private readonly ?string $unitCode = null,
        private readonly ?DocumentUnitOfMeasureScheme $scheme = null,
    ) {
        if ($value === '') {
            throw new InvalidArgumentException('Additional attribute value must not be empty.');
        }
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

    public function getScheme(): ?DocumentUnitOfMeasureScheme
    {
        return $this->scheme;
    }
}
