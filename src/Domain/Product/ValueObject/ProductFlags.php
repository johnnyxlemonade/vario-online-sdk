<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\ValueObject;

/**
 * Class ProductFlags
 *
 * Collection of product flags describing product state and marketing labels.
 *
 * Flags represent boolean attributes assigned to a product in Vario ERP
 * (e.g. sale, new product, discount, clearance). Internally they are stored
 * as a typed collection of {@see ProductFlag} enums.
 *
 * The value object provides convenient semantic helpers such as
 * {@see isSale()} or {@see isNew()} while keeping the underlying storage
 * normalized.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class ProductFlags implements ProductSection
{
    /** @var array<string,ProductFlag> */
    private array $flags = [];

    /**
     * @param list<ProductFlag> $flags
     */
    public static function fromFlags(array $flags): self
    {
        $self = new self();

        foreach ($flags as $flag) {
            $self->flags[$flag->value] = $flag;
        }

        return $self;
    }

    public function has(ProductFlag $flag): bool
    {
        return isset($this->flags[$flag->value]);
    }

    public function isSale(): bool
    {
        return $this->has(ProductFlag::SALE);
    }

    public function isNew(): bool
    {
        return $this->has(ProductFlag::NEW);
    }

    public function isDiscount(): bool
    {
        return $this->has(ProductFlag::DISCOUNT);
    }

    public function isClearance(): bool
    {
        return $this->has(ProductFlag::CLEARANCE);
    }

    public function isRecommended(): bool
    {
        return $this->has(ProductFlag::RECOMMENDED);
    }

    public function isPreparing(): bool
    {
        return $this->has(ProductFlag::PREPARING);
    }

    public function set(ProductFlag $flag, bool $enabled): void
    {
        if ($enabled) {
            $this->flags[$flag->value] = $flag;
        } else {
            unset($this->flags[$flag->value]);
        }
    }

    /**
     * @return list<ProductFlag>
     */
    public function all(): array
    {
        return array_values($this->flags);
    }

    /**
     * @return array{
     *     sale: bool,
     *     new: bool,
     *     discount: bool,
     *     clearance: bool,
     *     recommended: bool,
     *     preparing: bool
     * }
     */
    public function toArray(): array
    {
        return [
            'sale' => $this->isSale(),
            'new' => $this->isNew(),
            'discount' => $this->isDiscount(),
            'clearance' => $this->isClearance(),
            'recommended' => $this->isRecommended(),
            'preparing' => $this->isPreparing(),
        ];
    }
}
