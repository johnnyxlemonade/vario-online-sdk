<?php

declare(strict_types=1);

namespace Lemonade\Vario\Auth\Session;

use const PHP_SESSION_ACTIVE;

/**
 * Class NativePhpSession
 *
 * Default SessionInterface implementation based on native PHP sessions.
 *
 * This adapter provides a thin wrapper around the global $_SESSION
 * storage and exposes it through the SessionInterface abstraction
 * used by the Vario SDK.
 *
 * The class allows the SDK to interact with session storage without
 * depending directly on PHP superglobals, while still supporting
 * applications that rely on standard PHP session handling.
 *
 * Requires an active PHP session (session_start()) before usage.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Auth\Session
 * @category    Storage
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class NativePhpSession implements SessionInterface
{
    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function isActive(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }
}
