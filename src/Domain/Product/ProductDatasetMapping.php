<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product;

use Lemonade\Vario\Domain\Product\Mapper\ProductSectionMapper;
use Lemonade\Vario\Domain\Product\ValueObject\ProductSection;

/**
 * Class ProductDatasetMapping
 *
 * Defines mapping between DatasetView columns and Product domain fields.
 *
 * Allows customizing DatasetView column names used when mapping
 * product catalogue data into Product domain objects.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class ProductDatasetMapping
{
    /** @var ProductSectionMapper<ProductSection>[] */
    private array $components = [];

    /**
     * @param ProductSectionMapper<ProductSection> $mapper
     */
    public function add(ProductSectionMapper $mapper): self
    {
        $this->components[] = $mapper;

        return $this;
    }

    /** @return ProductSectionMapper<ProductSection>[] */
    public function components(): array
    {
        return $this->components;
    }
}
