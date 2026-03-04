<?php declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\ValueObject;

/**
 * Class ProductAttributes
 *
 * Represents a flexible collection of product attributes.
 *
 * Attributes typically describe product properties such as
 * color, size, material, capacity, etc. Unlike strongly typed
 * value objects, attributes allow dynamic key/value pairs
 * coming from ERP datasets or catalogue systems.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class ProductAttributes
{
    /**
     * @param array<string, scalar|null> $attributes
     */
    public function __construct(
        private readonly array $attributes = [],
    ) {}

    /**
     * @return array<string, scalar|null>
     */
    public function all(): array
    {
        return $this->attributes;
    }

    public function get(string $key): string|int|float|bool|null
    {
        return $this->attributes[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * @return array<string, scalar|null>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
