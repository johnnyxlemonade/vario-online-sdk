<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapping;

/**
 * Interface ProductSectionMapping
 *
 * Marker interface representing configuration used by a ProductSectionMapper
 * to translate DatasetView columns into a specific ProductSection value object.
 *
 * Mapping classes define which DatasetView columns correspond to fields
 * in a particular product section (e.g. identity, pricing, inventory).
 * They contain only configuration and no mapping logic.
 *
 * Each mapping instance is injected into a corresponding
 * ProductSectionMapper implementation which performs the actual
 * transformation from DatasetRow to ProductSection.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
interface ProductSectionMapping {}
