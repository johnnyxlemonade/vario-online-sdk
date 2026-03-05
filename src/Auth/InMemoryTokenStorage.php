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
final class InMemoryTokenStorage implements TokenStorageInterface
{
    private ?Token $token = null;

    public function get(): ?Token
    {
        if ($this->token === null) {
            return null;
        }

        if ($this->token->isExpired()) {
            $this->clear();
            return null;
        }

        return $this->token;
    }

    public function store(Token $token): void
    {
        $this->token = $token;
    }

    public function clear(): void
    {
        $this->token = null;
    }
}
