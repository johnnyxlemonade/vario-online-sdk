<?php

declare(strict_types=1);

namespace Lemonade\Vario\Query\Filter;

abstract class AbstractFilter implements QueryFilterInterface
{
    /**
     * @param array<string,mixed> $condition
     * @return list<list<array<string,mixed>>>
     */
    protected function group(array $condition): array
    {
        $group = [$condition];

        return [$group];
    }
}
