<?php

declare(strict_types=1);

namespace Lemonade\Vario\ValueObject;

interface PagedQueryInterface
{
    public function getPageIndex(): int;

    public function getPageLength(): int;

    /**
     * Serializace do formátu očekávaného Vario endpointem.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array;
}
