<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\ValueObject;

/**
 * Interface ProductSection
 *
 * Marker interface representing a section of the Product domain model.
 *
 * Each implementation encapsulates a specific part of product data
 * retrieved from a DatasetView response (e.g. identity, pricing,
 * inventory, dimensions, classification, attributes, etc.).
 *
 * Product sections are immutable value objects created by
 * ProductSectionMapper implementations and aggregated inside
 * the Product entity.
 *
 * Every section must provide a normalized array representation
 * suitable for serialization, debugging or API responses.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
interface ProductSection
{
    /**
     * Returns a normalized array representation of the section.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array;
}
