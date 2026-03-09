<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Pricing;

use IteratorAggregate;
use Traversable;

/**
 * Class PriceCollection
 *
 * Represents a collection of product price levels.
 *
 * Provides lookup and iteration over price levels
 * defined for a product.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 *
 * @implements IteratorAggregate<string,PriceLevel>
 */
final class PriceCollection implements IteratorAggregate
{
    /** @var array<string,PriceLevel> */
    private array $levels = [];

    public function add(PriceLevel $level): void
    {
        $this->levels[$level->getCode()] = $level;
    }

    public function get(string $code): ?PriceLevel
    {
        return $this->levels[$code] ?? null;
    }

    public function isEmpty(): bool
    {
        return $this->levels === [];
    }

    public function count(): int
    {
        return \count($this->levels);
    }

    /**
     * @return array<string,PriceLevel>
     */
    public function all(): array
    {
        return $this->levels;
    }

    /**
     * @return Traversable<string,PriceLevel>
     */
    public function getIterator(): Traversable
    {
        yield from $this->levels;
    }

    /**
     * @return array<string,array{
     *     code: string,
     *     price: array{
     *         value: float,
     *         includesVat: bool,
     *         vatRate: ?float,
     *         currency: ?string
     *     }
     * }>
     */
    public function toArray(): array
    {
        return array_map(
            static fn(PriceLevel $level) => $level->toArray(),
            $this->levels
        );
    }
}
