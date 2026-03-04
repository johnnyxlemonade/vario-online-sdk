<?php declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product;

use Lemonade\Vario\Domain\Product\ValueObject\ProductIdentity;
use Lemonade\Vario\Domain\Product\ValueObject\ProductDescription;
use Lemonade\Vario\Domain\Product\ValueObject\ProductFlags;
use Lemonade\Vario\Domain\Product\ValueObject\ProductDimensions;
use Lemonade\Vario\Domain\Product\ValueObject\ProductPricing;
use Lemonade\Vario\Domain\Product\ValueObject\ProductInventory;
use Lemonade\Vario\Domain\Product\ValueObject\ProductIdentifiers;
use Lemonade\Vario\Domain\Product\ValueObject\ProductClassification;
use Lemonade\Vario\Domain\Product\ValueObject\ProductAttributes;

/**
 * Class Product
 *
 * Domain representation of a product record retrieved from Vario DatasetView.
 *
 * Represents a normalized read model used by the SDK to expose
 * product catalogue data (SKU, dimensions, stock, pricing, etc.)
 * independent of the underlying DatasetView column names.
 *
 * The object is immutable and intended to be created by a mapper
 * that converts DatasetView rows into Product instances.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class Product
{
    public function __construct(
        private readonly ProductIdentity $identity,
        private readonly ?ProductDescription $description,
        private readonly ?ProductFlags $flags,
        private readonly ?ProductDimensions $dimensions,
        private readonly ?ProductPricing $pricing,
        private readonly ?ProductInventory $inventory,
        private readonly ?ProductIdentifiers $identifiers,
        private readonly ?ProductClassification $classification,
        private readonly ?ProductAttributes $attributes,
    ) {}

    public function getIdentity(): ProductIdentity
    {
        return $this->identity;
    }

    public function getDescription(): ?ProductDescription
    {
        return $this->description;
    }

    public function getFlags(): ?ProductFlags
    {
        return $this->flags;
    }

    public function getDimensions(): ?ProductDimensions
    {
        return $this->dimensions;
    }

    public function getPricing(): ?ProductPricing
    {
        return $this->pricing;
    }

    public function getInventory(): ?ProductInventory
    {
        return $this->inventory;
    }

    public function getIdentifiers(): ?ProductIdentifiers
    {
        return $this->identifiers;
    }

    public function getClassification(): ?ProductClassification
    {
        return $this->classification;
    }

    public function getAttributes(): ?ProductAttributes
    {
        return $this->attributes;
    }

}
