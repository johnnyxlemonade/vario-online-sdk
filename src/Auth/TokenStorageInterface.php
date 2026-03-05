<?php

declare(strict_types=1);

namespace Lemonade\Vario\Auth;

/**
 * Class InMemoryTokenStorage
 *
 * Simple in-memory implementation of TokenStorageInterface.
 * Used for temporarily storing access tokens during the application's runtime.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Auth
 * @category    Storage
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
interface TokenStorageInterface
{
    public function get(): ?Token;
    public function store(Token $token): void;
    public function clear(): void;
}
