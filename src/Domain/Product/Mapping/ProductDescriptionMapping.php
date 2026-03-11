<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapping;

/**
 * Class ProductDescriptionMapping
 *
 * Defines mapping between DatasetView columns and product description fields.
 *
 * Product descriptions typically include a short annotation and a full
 * textual description used in catalogues or e-commerce integrations.
 * This mapping allows adapting DatasetView column names to the SDK's
 * ProductDescription value object without modifying the domain model.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class ProductDescriptionMapping implements ProductSectionMapping
{
    public function __construct(
        private readonly ?string $shortDescription = 'ShortDescription',
        private readonly ?string $description = 'Description',
    ) {}

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
