<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product;

use Lemonade\Vario\Domain\Product\ValueObject\ProductAttributes;
use Lemonade\Vario\Domain\Product\ValueObject\ProductClassification;
use Lemonade\Vario\Domain\Product\ValueObject\ProductDescription;
use Lemonade\Vario\Domain\Product\ValueObject\ProductDimensions;
use Lemonade\Vario\Domain\Product\ValueObject\ProductFlags;
use Lemonade\Vario\Domain\Product\ValueObject\ProductIdentifiers;
use Lemonade\Vario\Domain\Product\ValueObject\ProductIdentity;
use Lemonade\Vario\Domain\Product\ValueObject\ProductInventory;
use Lemonade\Vario\Domain\Product\ValueObject\ProductPricing;
use Lemonade\Vario\Domain\Product\ValueObject\ProductSection;

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
    /** @var array<class-string<ProductSection>, ProductSection> */
    private array $sections = [];

    /**
     * @param iterable<ProductSection> $sections
     */
    public function __construct(iterable $sections)
    {
        foreach ($sections as $section) {
            $this->sections[$section::class] = $section;
        }
    }

    /**
     * @template T of ProductSection
     * @param class-string<T> $section
     * @return T|null
     */
    public function get(string $section): ?ProductSection
    {
        $value = $this->sections[$section] ?? null;

        /** @var T|null $value */
        return $value;
    }

    /**
     * @return array<class-string<ProductSection>, ProductSection>
     */
    public function all(): array
    {
        return $this->sections;
    }

    public function has(string $section): bool
    {
        return isset($this->sections[$section]);
    }

    // ----- Core typed helpers -----

    public function identity(): ?ProductIdentity
    {
        return $this->get(ProductIdentity::class);
    }

    public function description(): ?ProductDescription
    {
        return $this->get(ProductDescription::class);
    }

    public function flags(): ?ProductFlags
    {
        return $this->get(ProductFlags::class);
    }

    public function dimensions(): ?ProductDimensions
    {
        return $this->get(ProductDimensions::class);
    }

    public function pricing(): ?ProductPricing
    {
        return $this->get(ProductPricing::class);
    }

    public function inventory(): ?ProductInventory
    {
        return $this->get(ProductInventory::class);
    }

    public function identifiers(): ?ProductIdentifiers
    {
        return $this->get(ProductIdentifiers::class);
    }

    public function classification(): ?ProductClassification
    {
        return $this->get(ProductClassification::class);
    }

    public function attributes(): ?ProductAttributes
    {
        return $this->get(ProductAttributes::class);
    }
}
