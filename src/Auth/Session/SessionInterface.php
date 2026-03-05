<?php declare(strict_types=1);

namespace Lemonade\Vario\Auth\Session;

/**
 * Interface SessionInterface
 *
 * Minimal abstraction for session storage used by the Vario SDK.
 *
 * Allows the SDK to operate independently from the native PHP
 * session implementation while still supporting integrations
 * with framework session layers (Symfony, Laravel, Nette, etc.).
 *
 * Implementations may wrap:
 *  - native PHP sessions ($_SESSION)
 *  - framework session services
 *  - custom storage layers
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Auth
 * @category    Storage
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
interface SessionInterface
{
    public function get(string $key): mixed;
    public function set(string $key, mixed $value): void;
    public function has(string $key): bool;
    public function remove(string $key): void;
    public function isActive(): bool;
}