<?php declare(strict_types=1);

namespace Lemonade\Vario\Auth;

/**
 * Class Token
 *
 * Value object reprezentující access token vydaný Vario API.
 * Zapouzdřuje samotnou hodnotu tokenu a volitelnou informaci
 * o jeho expiraci v UTC.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Auth
 * @category    ValueObject
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class Token
{
    public function __construct(
        public readonly string $value,
        private readonly ?\DateTimeImmutable $expiresAtUtc = null
    ) {}

    /**
     * Vytvoří instanci tokenu přímo z odpovědi Vario API.
     *
     * @param array<string,mixed> $data
     */
    public static function fromApiResponse(array $data): self
    {
        $rawValue = $data['AccessToken'] ?? null;

        // PHPStan level 9 safe narrowing
        $value = is_string($rawValue) ? $rawValue : '';

        $expiration = $data['ExpirationUtc'] ?? null;

        $expiresAt = null;
        if (is_string($expiration) && $expiration !== '') {
            try {
                $expiresAt = new \DateTimeImmutable(
                    $expiration,
                    new \DateTimeZone('UTC')
                );
            } catch (\Throwable) {
                $expiresAt = null;
            }
        }

        return new self($value, $expiresAt);
    }

    public function getExpiresAtUtc(): ?\DateTimeImmutable
    {
        return $this->expiresAtUtc;
    }

    public function isExpired(
        \DateTimeImmutable $nowUtc = new \DateTimeImmutable('now', new \DateTimeZone('UTC'))
    ): bool {
        return $this->expiresAtUtc !== null && $nowUtc >= $this->expiresAtUtc;
    }
}
