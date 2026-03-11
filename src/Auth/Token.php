<?php

declare(strict_types=1);

namespace Lemonade\Vario\Auth;

use Lemonade\Vario\VarioClientConfig;

/**
 * Class Token
 *
 * Value object representing the access token issued by the Vario API.
 * Encapsulates the token value and optional information about its expiration in UTC.
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
        private readonly ?\DateTimeImmutable $expiresAtUtc = null,
        private readonly ?string $configHash = null
    ) {}

    /**
     * Create token from serialized storage payload.
     *
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): ?self
    {
        // Ensure 'value' is a string, otherwise return null (invalid token data)
        $value = is_string($data['value'] ?? null) ? $data['value'] : null;
        if ($value === null) {
            return null;
        }

        $expires = $data['expiresAtUtc'] ?? null;
        $expiresAt = null;

        if (is_string($expires) && $expires !== '') {
            try {
                $expiresAt = new \DateTimeImmutable($expires, new \DateTimeZone('UTC'));
            } catch (\Throwable) {
                $expiresAt = null;
            }
        }

        $configHash = is_string($data['configHash'] ?? null)
            ? $data['configHash']
            : null;

        return new self(
            value: $value,
            expiresAtUtc: $expiresAt,
            configHash: $configHash
        );
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

    /**
     * @return array<string,string|null>
     */
    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'expiresAtUtc' => $this->expiresAtUtc?->format('Y-m-d\TH:i:s\Z'),
            'configHash' => $this->configHash,
        ];
    }

    public function getConfigHash(): ?string
    {
        return $this->configHash;
    }

    public static function buildConfigHash(VarioClientConfig $config): string
    {
        return sha1(
            $config->getBaseUrl()
            . '|' . $config->getLoginName()
            . '|' . $config->getCompanyNumber()
        );
    }
}
