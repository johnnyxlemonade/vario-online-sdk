<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapping\ProductFlagsMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductFlag;
use Lemonade\Vario\Domain\Product\ValueObject\ProductFlags;

/**
 * Class ProductFlagsMapper
 *
 * Maps product marketing and state flags from DatasetView rows
 * into the ProductFlags value object.
 *
 * The mapper uses ProductFlagsMapping to determine which DatasetView
 * columns represent individual flags such as sale, new product,
 * discount, clearance, recommended or preparing.
 *
 * Each configured column is interpreted as a boolean value and
 * translated into a corresponding ProductFlag enum entry.
 *
 * If no flags are enabled for the given dataset row, the mapper
 * returns null and the flags section is omitted from the Product object.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 *
 * @extends AbstractProductSectionMapper<ProductFlags>
 */
final class ProductFlagsMapper extends AbstractProductSectionMapper
{
    public function __construct(private readonly ProductFlagsMapping $mapping) {}

    public function map(DatasetRow $row): ?ProductFlags
    {
        $flags = new ProductFlags();

        $flags->set(ProductFlag::SALE, $this->mapBoolFlag($row, $this->mapping->isSale()));
        $flags->set(ProductFlag::NEW, $this->mapBoolFlag($row, $this->mapping->isNew()));
        $flags->set(ProductFlag::DISCOUNT, $this->mapBoolFlag($row, $this->mapping->isDiscount()));
        $flags->set(ProductFlag::CLEARANCE, $this->mapBoolFlag($row, $this->mapping->isClearance()));
        $flags->set(ProductFlag::RECOMMENDED, $this->mapBoolFlag($row, $this->mapping->isRecommended()));
        $flags->set(ProductFlag::PREPARING, $this->mapBoolFlag($row, $this->mapping->isPreparing()));

        if ($flags->all() === []) {
            return null;
        }

        return $flags;
    }
}
