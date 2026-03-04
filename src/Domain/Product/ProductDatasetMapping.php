<?php declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product;

use Lemonade\Vario\Domain\Product\Mapping\ProductDescriptionMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductDimensionsMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductFlagsMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductIdentityMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductInventoryMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductPricingMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductAttributesMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductClassificationMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductIdentifiersMapping;

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
    public function __construct(
        private readonly ProductIdentityMapping $identity = new ProductIdentityMapping(),
        private readonly ProductDescriptionMapping $description = new ProductDescriptionMapping(),
        private readonly ProductFlagsMapping $flags = new ProductFlagsMapping(),
        private readonly ProductDimensionsMapping $dimensions = new ProductDimensionsMapping(),
        private readonly ProductPricingMapping $pricing = new ProductPricingMapping(),
        private readonly ProductInventoryMapping $inventory = new ProductInventoryMapping(),
        private readonly ProductIdentifiersMapping $identifiers = new ProductIdentifiersMapping(),
        private readonly ProductClassificationMapping $classification = new ProductClassificationMapping(),
        private readonly ProductAttributesMapping $attributes = new ProductAttributesMapping(),
    ) {}

    public function identity(): ProductIdentityMapping
    {
        return $this->identity;
    }

    public function description(): ProductDescriptionMapping
    {
        return $this->description;
    }

    public function flags(): ProductFlagsMapping
    {
        return $this->flags;
    }

    public function dimensions(): ProductDimensionsMapping
    {
        return $this->dimensions;
    }

    public function pricing(): ProductPricingMapping
    {
        return $this->pricing;
    }

    public function inventory(): ProductInventoryMapping
    {
        return $this->inventory;
    }

    public function identifiers(): ProductIdentifiersMapping
    {
        return $this->identifiers;
    }

    public function classification(): ProductClassificationMapping
    {
        return $this->classification;
    }

    public function attributes(): ProductAttributesMapping
    {
        return $this->attributes;
    }
}
