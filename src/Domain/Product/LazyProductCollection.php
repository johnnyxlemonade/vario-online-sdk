<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product;

use IteratorAggregate;
use Traversable;

/**
 * Class LazyProductCollection
 *
 * Lazy iterable collection of Product domain objects.
 *
 * Unlike ProductCollection, this implementation does not materialize
 * all Product instances in memory. Instead it wraps an iterable
 * (typically a generator returned by ProductMapper::iterate()) and
 * processes items lazily during iteration.
 *
 * This is useful when working with very large DatasetView responses,
 * where creating a full in-memory collection would be inefficient.
 *
 * LazyProductCollection also allows building simple processing
 * pipelines such as filtering before iterating the results.
 *
 * Example usage:
 *
 * $products = $mapper
 *     ->lazy($rows)
 *     ->filter(fn(Product $p) => $p->inventory()?->getStock() > 0);
 *
 * foreach ($products as $product) {
 *     echo $product->identity()?->getName();
 * }
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
final class LazyProductCollection implements IteratorAggregate
{
    /** @var callable():iterable<int, Product> */
    private $factory;

    /**
     * @param callable():iterable<int, Product> $factory
     */
    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return Traversable<int, Product>
     */
    public function getIterator(): Traversable
    {
        foreach (($this->factory)() as $item) {
            yield $item;
        }
    }

    public function collect(): ProductCollection
    {
        return new ProductCollection(
            ...iterator_to_array($this->getIterator(), false)
        );
    }

    /**
     * @param callable(Product):bool $predicate
     */
    public function filter(callable $predicate): self
    {
        return new self(function () use ($predicate) {

            foreach (($this->factory)() as $item) {
                if ($predicate($item)) {
                    yield $item;
                }
            }

        });
    }

    public function first(): ?Product
    {
        foreach (($this->factory)() as $item) {
            return $item;
        }

        return null;
    }
}
