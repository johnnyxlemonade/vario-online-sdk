<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\OutgoingQuotation\ValueObject;

final class OutgoingQuotationDescription
{
    public function __construct(
        private readonly string $text,
        private readonly ?string $langId = null,
    ) {}

    public function getText(): string
    {
        return $this->text;
    }

    public function getLangId(): ?string
    {
        return $this->langId;
    }
}
