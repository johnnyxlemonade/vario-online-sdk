<?php declare(strict_types=1);

namespace Lemonade\Vario\Auth\Storage;

use Lemonade\Vario\Auth\Session\SessionInterface;
use Lemonade\Vario\Auth\Token;

/**
 * Class SessionTokenStorage
 *
 * Stores the access token inside a session storage implementation.
 *
 * The SDK does not depend directly on PHP native sessions. Instead,
 * a SessionInterface implementation is injected, allowing integration
 * with native PHP sessions or framework session layers.
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
        private readonly SessionInterface $session,
        private readonly string $key = '_lemonade_vario_auth_token'
    ) {
        if (!$this->session->isActive()) {
            throw new \RuntimeException(
                'SessionTokenStorage requires an active session.'
            );
        }
    }

    public function get(): ?Token
    {
        if ($this->token !== null) {
            return $this->token;
        }

        $data = $this->session->get($this->key);

        if (!is_array($data)) {
            return null;
        }

        /** @var array<string,mixed> $data */
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
        $this->session->set($this->key, $token->toArray());
    }

    public function clear(): void
    {
        $this->token = null;
        $this->session->remove($this->key);
    }
}