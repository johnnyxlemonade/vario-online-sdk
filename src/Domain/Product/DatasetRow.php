<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product;

/**
 * Class DatasetRow
 *
 * Lightweight wrapper around a single DatasetView row returned by Vario API.
 *
 * Provides safe and typed accessors for dataset columns while keeping
 * the original raw structure intact. This object acts as a small
 * anti-corruption layer between the generic DatasetView response
 * and domain mappers (e.g. ProductMapper).
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class DatasetRow
{
    /** @param array<string,mixed> $data */
    public function __construct(
        private array $data
    ) {}

    public function get(string $field): mixed
    {
        return $this->data[$field] ?? null;
    }

    public function getString(string $field): ?string
    {
        $value = $this->normalizeScalar($field);

        return $value !== null ? (string) $value : null;
    }

    public function getFloat(string $field): ?float
    {
        $value = $this->normalizeScalar($field);

        if ($value === null) {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        return null;
    }

    public function getInt(string $field): ?int
    {
        $value = $this->normalizeScalar($field);

        if ($value === null) {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return null;
    }

    public function getBool(string $field): ?bool
    {
        $value = $this->normalizeScalar($field);

        if ($value === null) {
            return null;
        }

        return (bool) $value;
    }

    public function getNullableString(string $field): ?string
    {
        return $this->getString($field);
    }

    public function getNullableFloat(string $field): ?float
    {
        return $this->getFloat($field);
    }

    public function has(string $field): bool
    {
        return array_key_exists($field, $this->data);
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return $this->data;
    }

    private function normalizeScalar(string $field): int|float|string|bool|null
    {
        $value = $this->get($field);

        if ($value === null || $value === '') {
            return null;
        }

        if (is_scalar($value)) {
            return $value;
        }

        return null;
    }

}
