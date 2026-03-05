<?php

declare(strict_types=1);

/**
 * Interface QueryFilterInterface
 *
 * Contract for all query filters used in Vario dataset queries.
 * Each filter must serialize itself into a transport structure
 * compatible with the Vario API FilterCriteria format.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Query\Filter
 * @category    Query
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */

namespace Lemonade\Vario\Query\Filter;

interface QueryFilterInterface
{
    /**
     * Returns normalized filter payload for Vario API.
     *
     * @return array<int, array<int, array<string,mixed>>>
     */
    public function toArray(): array;
}
