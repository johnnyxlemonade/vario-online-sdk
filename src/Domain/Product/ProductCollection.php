<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Class ProductCollection
 *
 * Typed collection of Product domain objects.
 *
 * The collection provides a convenient iterable wrapper around
 * multiple Product instances returned from DatasetView responses.
 * It allows working with product results in a type-safe way while
 * keeping the domain layer independent from raw API responses.
 *
 * ProductCollection is typically created after mapping DatasetView
 * rows using the ProductMapper and can be iterated using standard
 * PHP foreach constructs.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 *
 * @implements IteratorAggregate<int, Product>
 */
final class ProductCollection implements IteratorAggregate, Countable
{
    /** @var list<Product> */
    private array $items;

    public function __construct(Product ...$items)
    {
        $normalized = array_values($items);

        /** @var list<Product> $normalized */
        $this->items = $normalized;
    }

    /**
     * @return Traversable<int, Product>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    public function first(): ?Product
    {
        return $this->items[0] ?? null;
    }

    /**
     * @param callable(Product):bool $predicate
     */
    public function find(callable $predicate): ?Product
    {
        foreach ($this->items as $item) {
            if ($predicate($item)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param callable(Product):bool $predicate
     */
    public function filter(callable $predicate): self
    {
        return new self(
            ...array_values(array_filter($this->items, $predicate))
        );
    }

    /**
     * @template T
     * @param callable(Product):T $mapper
     * @return list<T>
     */
    public function map(callable $mapper): array
    {
        return array_map($mapper, $this->items);
    }

    /**
     * @return list<Product>
     */
    public function toArray(): array
    {
        return $this->items;
    }
}
