<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product;

/**
 * Class ProductMapper
 *
 * Maps DatasetView rows into Product domain objects.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class ProductMapper
{
    public function __construct(
        private readonly ProductDatasetMapping $mapping
    ) {}

    public function map(DatasetRow $row): Product
    {
        $sections = [];

        foreach ($this->mapping->components() as $mapper) {
            $section = $mapper->map($row);

            if ($section !== null) {
                $sections[] = $section;
            }
        }

        return new Product($sections);
    }

    /**
     * @param iterable<int, array<string,mixed>|DatasetRow> $rows
     * @return \Generator<int, Product>
     */
    public function iterate(iterable $rows): \Generator
    {
        foreach ($rows as $row) {

            if (!$row instanceof DatasetRow) {
                $row = new DatasetRow($row);
            }

            yield $this->map($row);
        }
    }

    /**
     * @param iterable<int, array<string,mixed>|DatasetRow> $rows
     */
    public function collect(iterable $rows): ProductCollection
    {
        return new ProductCollection(
            ...$this->iterate($rows)
        );
    }

    /**
     * @param iterable<int, array<string,mixed>|DatasetRow> $rows
     */
    public function lazy(iterable $rows): LazyProductCollection
    {
        return new LazyProductCollection(
            fn() => $this->iterate($rows)
        );
    }

}
