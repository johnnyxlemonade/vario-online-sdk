<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\ValueObject\ProductSection;

/**
 * Interface ProductSectionMapper
 *
 * Defines a contract for mapping a DatasetView row into a specific
 * ProductSection value object.
 *
 * Implementations are responsible for extracting relevant data
 * from a DatasetRow and constructing the corresponding domain
 * value object representing a part of the Product aggregate
 * (e.g. identity, pricing, inventory, dimensions, etc.).
 *
 * Each mapper focuses on a single product section and may return
 * null if the dataset row does not contain any relevant data for
 * that section.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 *
 * @template-covariant T of ProductSection
 */
interface ProductSectionMapper
{
    /**
     * @return T|null
     */
    public function map(DatasetRow $row): ?ProductSection;
}
