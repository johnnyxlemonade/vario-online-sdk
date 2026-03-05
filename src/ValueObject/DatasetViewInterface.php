<?php

declare(strict_types=1);

namespace Lemonade\Vario\ValueObject;

interface DatasetViewInterface
{
    /**
     * Agenda pro Vario API.
     */
    public function agenda(): string;

    /**
     * DatasetViewKey (např. Katalog/StromAPI).
     */
    public function key(): string;
}
