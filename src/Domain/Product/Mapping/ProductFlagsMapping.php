<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Mapping;

/**
 * Class ProductFlagsMapping
 *
 * Defines mapping between DatasetView columns and product flag fields.
 *
 * Product flags describe special states or marketing labels assigned
 * to products, such as sale, new product, discount, clearance,
 * recommended item or upcoming product.
 *
 * This mapping allows adapting DatasetView column names used by
 * the ERP system to the SDK's ProductFlags value object without
 * modifying the domain model.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class ProductFlagsMapping implements ProductSectionMapping
{
    public function __construct(
        private readonly ?string $sale = 'Sale',
        private readonly ?string $new = 'New',
        private readonly ?string $discount = 'OnSale',
        private readonly ?string $clearance = 'Clearance',
        private readonly ?string $recommended = 'Recomended',
        private readonly ?string $preparing = 'Preparing',
    ) {}

    public function isSale(): ?string
    {
        return $this->sale;
    }

    public function isNew(): ?string
    {
        return $this->new;
    }

    public function isDiscount(): ?string
    {
        return $this->discount;
    }

    public function isClearance(): ?string
    {
        return $this->clearance;
    }

    public function isRecommended(): ?string
    {
        return $this->recommended;
    }

    public function isPreparing(): ?string
    {
        return $this->preparing;
    }
}
