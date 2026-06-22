<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\OutgoingQuotation\ValueObject;

use Lemonade\Vario\Domain\OutgoingQuotation\Enum\OutgoingQuotationUnitOfMeasureScheme;

final class OutgoingQuotationQuantity
{
    public function __construct(
        private readonly float $value,
        private readonly string $unitCode,
        private readonly OutgoingQuotationUnitOfMeasureScheme $scheme = OutgoingQuotationUnitOfMeasureScheme::Unknown,
    ) {}

    public function getValue(): float
    {
        return $this->value;
    }

    public function getUnitCode(): string
    {
        return $this->unitCode;
    }

    public function getScheme(): OutgoingQuotationUnitOfMeasureScheme
    {
        return $this->scheme;
    }
}
