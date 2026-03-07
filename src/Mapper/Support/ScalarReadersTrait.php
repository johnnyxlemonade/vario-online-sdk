<?php

declare(strict_types=1);

namespace Lemonade\Vario\Mapper\Support;

/**
 * Trait ScalarReadersTrait
 *
 * Utility trait providing safe scalar readers for transport → domain
 * mapping layers.
 *
 * Vario API responses frequently contain loosely typed values
 * (`mixed`) coming from JSON payloads. These helpers provide
 * controlled narrowing and normalization when converting raw
 * API data into strongly typed domain models.
 *
 * The trait offers convenience readers for common scalar types:
 *
 * - stringOrNull()
 * - string()
 * - intOrNull()
 * - floatOrNull()
 * - nullableTrim()
 *
 * These helpers are primarily used inside mapper classes
 * (e.g. KnownPartyMapper) to safely extract values from
 * untrusted API payloads while maintaining strict typing.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Mapper
 * @category    Support
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrák
 * @license     MIT
 * @since       1.0
 */
trait ScalarReadersTrait
{
    protected function stringOrNull(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_scalar($value)) {
            $v = trim((string) $value);
            return $v === '' ? null : $v;
        }

        return null;
    }

    protected function string(mixed $value): string
    {
        return $this->stringOrNull($value) ?? '';
    }

    protected function intOrNull(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return null;
    }

    protected function floatOrNull(mixed $value): ?float
    {
        if (is_float($value) || is_int($value)) {
            return (float) $value;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        return null;
    }

    protected function nullableTrim(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

}
