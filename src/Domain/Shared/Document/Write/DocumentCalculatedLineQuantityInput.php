<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\Write;

use InvalidArgumentException;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;

final class DocumentCalculatedLineQuantityInput
{
    public function __construct(
        private readonly float $value,
        private readonly string $unitCode,
        private readonly DocumentUnitOfMeasureScheme $scheme = DocumentUnitOfMeasureScheme::Unknown,
    ) {
        if ($value <= 0.0) {
            throw new InvalidArgumentException('Line quantity must be greater than zero.');
        }

        if ($unitCode === '') {
            throw new InvalidArgumentException('Line quantity unit code must not be empty.');
        }
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getUnitCode(): string
    {
        return $this->unitCode;
    }

    public function getScheme(): DocumentUnitOfMeasureScheme
    {
        return $this->scheme;
    }
}
