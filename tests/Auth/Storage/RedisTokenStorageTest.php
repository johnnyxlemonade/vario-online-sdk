<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Auth;

use Lemonade\Vario\Auth\Storage\RedisTokenStorage;
use Lemonade\Vario\Auth\Token;
use PHPUnit\Framework\TestCase;
use Redis;

final class RedisTokenStorageTest extends TestCase
{
    public function test_get_returns_null_when_key_missing(): void
    {
        $redis = $this->createMock(Redis::class);

        $redis->method('get')
            ->willReturn(false);

        $storage = new RedisTokenStorage($redis);

        self::assertNull($storage->get());
    }

    public function test_get_returns_null_when_invalid_json(): void
    {
        $redis = $this->createMock(Redis::class);

        $redis->method('get')
            ->willReturn('invalid-json');

        $storage = new RedisTokenStorage($redis);

        self::assertNull($storage->get());
    }

    public function test_get_returns_valid_token(): void
    {
        $redis = $this->createMock(Redis::class);

        $token = new Token('abc');

        $redis->method('get')
            ->willReturn(json_encode($token->toArray()));

        $storage = new RedisTokenStorage($redis);

        $loaded = $storage->get();

        self::assertInstanceOf(Token::class, $loaded);
        self::assertSame('abc', $loaded->value);
    }

    public function test_get_clears_expired_token(): void
    {
        $redis = $this->createMock(Redis::class);

        $expired = new Token(
            'abc',
            new \DateTimeImmutable('2000-01-01T00:00:00Z')
        );

        $redis->method('get')
            ->willReturn(json_encode($expired->toArray()));

        $redis->expects(self::once())
            ->method('del');

        $storage = new RedisTokenStorage($redis);

        self::assertNull($storage->get());
    }

    public function test_store_with_expiration(): void
    {
        $redis = $this->createMock(Redis::class);

        $token = new Token(
            'abc',
            new \DateTimeImmutable('+1 hour')
        );

        $redis
            ->expects(self::once())
            ->method('set');

        $storage = new RedisTokenStorage($redis);

        $storage->store($token);
    }

    public function test_store_without_expiration_uses_default_ttl(): void
    {
        $redis = $this->createMock(Redis::class);

        $token = new Token('abc');

        $redis
            ->expects(self::once())
            ->method('set')
            ->with(
                '_lemonade_vario_auth_token',
                self::anything(),
                3600
            );

        $storage = new RedisTokenStorage($redis);

        $storage->store($token);
    }

    public function test_clear_removes_key(): void
    {
        $redis = $this->createMock(Redis::class);

        $redis
            ->expects(self::once())
            ->method('del')
            ->with('_lemonade_vario_auth_token');

        $storage = new RedisTokenStorage($redis);

        $storage->clear();
    }
}
