<?php declare(strict_types=1);

namespace Lemonade\Vario\Auth\Storage;

use Lemonade\Vario\Auth\Token;
use Redis;

/**
 * Class RedisTokenStorage
 *
 * Stores the access token in Redis.
 * Useful for distributed applications and when high availability is required.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Auth
 * @category    Storage
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class RedisTokenStorage implements TokenStorageInterface
{

    private ?Token $token = null;

    public function __construct(
        private readonly Redis $redis,
        private readonly string $key = '_lemonade_vario_auth_token'
    ) {}

    public function get(): ?Token
    {
        $data = $this->redis->get($this->key);

        if (!is_string($data)) {
            return null;
        }

        $decoded = json_decode($data, true);

        if (!is_array($decoded)) {
            return null;
        }

        /** @var array<string, mixed> $decoded */
        $token = Token::fromArray($decoded);

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
        $expiresAt = $token->getExpiresAtUtc();

        $ttl = $expiresAt !== null
            ? $expiresAt->getTimestamp() - time()
            : 3600;

        $this->redis->set(
            $this->key,
            json_encode($token->toArray(), JSON_THROW_ON_ERROR),
            $ttl
        );
    }

    public function clear(): void
    {
        $this->token = null;
        $this->redis->del($this->key);
    }

}
