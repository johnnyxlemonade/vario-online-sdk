<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document\Write;

use InvalidArgumentException;

final class DocumentCalculatedLineIdentityInput
{
    public function __construct(
        private readonly string $uuid,
        private readonly ?string $id = null,
        private readonly ?string $note = null,
    ) {
        if ($uuid === '') {
            throw new InvalidArgumentException('Line UUID must not be empty.');
        }
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }
}
