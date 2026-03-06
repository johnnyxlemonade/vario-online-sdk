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
     * @param iterable<array<string,mixed>|DatasetRow> $rows
     * @return iterable<Product>
     */
    public function iterate(iterable $rows): iterable
    {
        foreach ($rows as $row) {

            if (!$row instanceof DatasetRow) {
                $row = new DatasetRow($row);
            }

            yield $this->map($row);
        }
    }

}
