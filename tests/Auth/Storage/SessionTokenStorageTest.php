<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Auth\Storage;

use Lemonade\Vario\Auth\Session\SessionInterface;
use Lemonade\Vario\Auth\Storage\SessionTokenStorage;
use Lemonade\Vario\Auth\Token;
use PHPUnit\Framework\TestCase;

final class SessionTokenStorageTest extends TestCase
{
    public function test_constructor_requires_active_session(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $session->method('isActive')->willReturn(false);

        $this->expectException(\RuntimeException::class);

        new SessionTokenStorage($session);
    }

    public function test_get_returns_null_when_session_has_no_data(): void
    {
        $session = $this->createMock(SessionInterface::class);

        $session->method('isActive')->willReturn(true);
        $session->method('get')->willReturn(null);

        $storage = new SessionTokenStorage($session);

        self::assertNull($storage->get());
    }

    public function test_get_returns_null_when_data_is_not_array(): void
    {
        $session = $this->createMock(SessionInterface::class);

        $session->method('isActive')->willReturn(true);
        $session->method('get')->willReturn('invalid');

        $storage = new SessionTokenStorage($session);

        self::assertNull($storage->get());
    }

    public function test_get_returns_null_and_clears_when_token_expired(): void
    {
        $session = $this->createMock(SessionInterface::class);

        $session->method('isActive')->willReturn(true);

        $expiredToken = new Token(
            'abc',
            new \DateTimeImmutable('2000-01-01T00:00:00Z')
        );

        $session->method('get')->willReturn($expiredToken->toArray());

        $session->expects(self::once())
            ->method('remove')
            ->with('_lemonade_vario_auth_token');

        $storage = new SessionTokenStorage($session);

        self::assertNull($storage->get());
    }

    public function test_get_loads_valid_token_from_session(): void
    {
        $session = $this->createMock(SessionInterface::class);

        $session->method('isActive')->willReturn(true);

        $token = new Token('abc');

        $session->method('get')->willReturn($token->toArray());

        $storage = new SessionTokenStorage($session);

        $loaded = $storage->get();

        self::assertInstanceOf(Token::class, $loaded);
        self::assertSame('abc', $loaded->value);
    }

    public function test_get_returns_cached_token(): void
    {
        $session = $this->createMock(SessionInterface::class);

        $session->method('isActive')->willReturn(true);

        $token = new Token('abc');

        $session->method('get')->willReturn($token->toArray());

        $storage = new SessionTokenStorage($session);

        $storage->get(); // load from session
        $cached = $storage->get(); // cached branch

        self::assertInstanceOf(Token::class, $cached);
        self::assertSame('abc', $cached->value);
    }

    public function test_store_persists_token(): void
    {
        $session = $this->createMock(SessionInterface::class);

        $session->method('isActive')->willReturn(true);

        $token = new Token('abc');

        $session->expects(self::once())
            ->method('set')
            ->with('_lemonade_vario_auth_token', $token->toArray());

        $storage = new SessionTokenStorage($session);

        $storage->store($token);
    }

    public function test_clear_removes_token(): void
    {
        $session = $this->createMock(SessionInterface::class);

        $session->method('isActive')->willReturn(true);

        $session->expects(self::once())
            ->method('remove')
            ->with('_lemonade_vario_auth_token');

        $storage = new SessionTokenStorage($session);

        $storage->clear();
    }
}
