<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\ValueObject;

final class DocumentDescription
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
