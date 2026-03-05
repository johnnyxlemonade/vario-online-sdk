<?php

declare(strict_types=1);

namespace Lemonade\Vario\Auth;

/**
 * Class SessionTokenStorage
 *
 * Stores the access token in PHP sessions.
 * Useful for web apps where token needs to be persisted across multiple user sessions.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Auth
 * @category    Storage
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class SessionTokenStorage implements TokenStorageInterface
{
    private ?Token $token = null;

    /**
     * @throws \RuntimeException
     */
    public function __construct(
        private readonly string $key = '_lemonade_vario_auth_token'
    ) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new \RuntimeException(
                'SessionTokenStorage requires an active session. Please call session_start() before initializing.'
            );
        }
    }

    public function get(): ?Token
    {
        if (!isset($_SESSION[$this->key]) || !is_array($_SESSION[$this->key])) {
            return null;
        }

        /** @var array<string, mixed> $data */
        $data = $_SESSION[$this->key];
        $token = Token::fromArray($data);

        if ($token === null || $token->isExpired()) {
            $this->clear();
            return null;
        }

        $this->token = $token;
        return $this->token;
    }

    public function store(Token $token): void
    {
        $this->token = $token;
        $_SESSION[$this->key] = $token->toArray();
    }

    public function clear(): void
    {
        $this->token = null;
        if (isset($_SESSION[$this->key])) {
            unset($_SESSION[$this->key]);
        }
    }
}
