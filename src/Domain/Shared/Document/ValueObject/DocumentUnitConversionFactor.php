<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\ValueObject;

use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;

final class DocumentUnitConversionFactor
{
    public function __construct(
        private readonly float $value,
        private readonly string $unitCode,
        private readonly DocumentUnitOfMeasureScheme $scheme = DocumentUnitOfMeasureScheme::Unknown,
    ) {}

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
