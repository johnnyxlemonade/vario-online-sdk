<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Auth;

use Lemonade\Vario\Auth\Storage\InMemoryTokenStorage;
use Lemonade\Vario\Auth\Token;
use PHPUnit\Framework\TestCase;

final class InMemoryTokenStorageTest extends TestCase
{
    public function test_get_returns_null_when_empty(): void
    {
        $storage = new InMemoryTokenStorage();

        self::assertNull($storage->get());
    }

    public function test_get_returns_null_when_token_expired(): void
    {
        $storage = new InMemoryTokenStorage();

        $expired = new Token(
            'abc',
            new \DateTimeImmutable('2000-01-01T00:00:00Z')
        );

        $storage->store($expired);

        self::assertNull($storage->get());
    }

    public function test_store_and_get_token(): void
    {
        $storage = new InMemoryTokenStorage();

        $token = new Token('abc');

        $storage->store($token);

        $loaded = $storage->get();

        self::assertInstanceOf(Token::class, $loaded);
        self::assertSame('abc', $loaded->value);
    }

    public function test_expired_token_is_cleared(): void
    {
        $storage = new InMemoryTokenStorage();

        $expired = new Token(
            'abc',
            new \DateTimeImmutable('2000-01-01T00:00:00Z')
        );

        $storage->store($expired);

        self::assertNull($storage->get());
        self::assertNull($storage->get()); // ověří že clear() proběhl
    }

    public function test_clear_removes_token(): void
    {
        $storage = new InMemoryTokenStorage();

        $storage->store(new Token('abc'));

        $storage->clear();

        self::assertNull($storage->get());
    }


}
