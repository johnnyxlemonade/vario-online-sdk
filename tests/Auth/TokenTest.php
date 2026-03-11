<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Auth;

use Lemonade\Vario\Auth\Token;
use PHPUnit\Framework\TestCase;

final class TokenTest extends TestCase
{
    public function test_token_creation(): void
    {
        $expires = new \DateTimeImmutable('2030-01-01T00:00:00Z');

        $token = new Token('abc', $expires);

        self::assertSame('abc', $token->value);
        self::assertSame($expires, $token->getExpiresAtUtc());
    }

    public function test_from_array_valid(): void
    {
        $token = Token::fromArray([
            'value' => 'abc',
            'expiresAtUtc' => '2030-01-01T00:00:00Z',
        ]);

        self::assertInstanceOf(Token::class, $token);
        self::assertSame('abc', $token->value);
        self::assertSame(
            '2030-01-01T00:00:00+00:00',
            $token->getExpiresAtUtc()?->format('c')
        );
    }

    public function test_from_array_without_expiration(): void
    {
        $token = Token::fromArray([
            'value' => 'abc',
        ]);

        self::assertInstanceOf(Token::class, $token);
        self::assertNull($token->getExpiresAtUtc());
    }

    public function test_from_array_invalid_value(): void
    {
        $token = Token::fromArray([
            'value' => 123,
        ]);

        self::assertNull($token);
    }

    public function test_from_array_invalid_date(): void
    {
        $token = Token::fromArray([
            'value' => 'abc',
            'expiresAtUtc' => 'invalid-date',
        ]);

        self::assertInstanceOf(Token::class, $token);
        self::assertNull($token->getExpiresAtUtc());
    }

    public function test_is_expired(): void
    {
        $expires = new \DateTimeImmutable('2000-01-01T00:00:00Z');

        $token = new Token('abc', $expires);

        self::assertTrue(
            $token->isExpired(
                new \DateTimeImmutable('2030-01-01T00:00:00Z')
            )
        );
    }

    public function test_is_not_expired(): void
    {
        $expires = new \DateTimeImmutable('2030-01-01T00:00:00Z');

        $token = new Token('abc', $expires);

        self::assertFalse(
            $token->isExpired(
                new \DateTimeImmutable('2020-01-01T00:00:00Z')
            )
        );
    }

    public function test_is_not_expired_when_no_expiration(): void
    {
        $token = new Token('abc');

        self::assertFalse($token->isExpired());
    }

    public function test_to_array(): void
    {
        $expires = new \DateTimeImmutable('2030-01-01T00:00:00Z');

        $token = new Token('abc', $expires);

        $array = $token->toArray();

        self::assertSame('abc', $array['value']);
        self::assertSame('2030-01-01T00:00:00Z', $array['expiresAtUtc']);
    }

    public function test_from_array_with_config_hash(): void
    {
        $token = Token::fromArray([
            'value' => 'abc',
            'configHash' => 'hash123',
        ]);

        self::assertInstanceOf(Token::class, $token);
        self::assertSame('hash123', $token->getConfigHash());
    }

    public function test_to_array_contains_config_hash(): void
    {
        $expires = new \DateTimeImmutable('2030-01-01T00:00:00Z');

        $token = new Token(
            value: 'abc',
            expiresAtUtc: $expires,
            configHash: 'hash123'
        );

        $array = $token->toArray();

        self::assertSame('abc', $array['value']);
        self::assertSame('2030-01-01T00:00:00Z', $array['expiresAtUtc']);
        self::assertSame('hash123', $array['configHash']);
    }
}
