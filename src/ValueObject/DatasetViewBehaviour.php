<?php

declare(strict_types=1);

namespace Lemonade\Vario\ValueObject;

trait DatasetViewBehaviour
{
    abstract protected function rawValue(): string;

    public function agenda(): string
    {
        return explode('/', $this->rawValue(), 2)[0];
    }

    public function key(): string
    {
        return $this->rawValue();
    }
}
