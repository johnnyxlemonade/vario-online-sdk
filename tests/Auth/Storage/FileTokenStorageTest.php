<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Auth;

use Lemonade\Vario\Auth\Storage\FileTokenStorage;
use Lemonade\Vario\Auth\Token;
use PHPUnit\Framework\TestCase;

final class FileTokenStorageTest extends TestCase
{
    private string $file;

    protected function setUp(): void
    {
        $this->file = sys_get_temp_dir() . '/vario_token_' . uniqid() . '.json';
    }

    protected function tearDown(): void
    {
        if (is_file($this->file)) {
            unlink($this->file);
        }
    }

    public function test_get_returns_null_when_file_missing(): void
    {
        $storage = new FileTokenStorage($this->file);

        self::assertNull($storage->get());
    }

    public function test_store_and_get_token(): void
    {
        $storage = new FileTokenStorage($this->file);

        $token = new Token('abc');

        $storage->store($token);

        $loaded = $storage->get();

        self::assertInstanceOf(Token::class, $loaded);
        self::assertSame('abc', $loaded->value);
    }

    public function test_get_returns_null_for_invalid_file_content(): void
    {
        file_put_contents($this->file, json_encode(['invalid' => true]));

        $storage = new FileTokenStorage($this->file);

        self::assertNull($storage->get());
    }

    public function test_get_clears_expired_token(): void
    {
        $expired = new Token(
            'abc',
            new \DateTimeImmutable('2000-01-01T00:00:00Z')
        );

        file_put_contents(
            $this->file,
            json_encode($expired->toArray(), JSON_THROW_ON_ERROR)
        );

        $storage = new FileTokenStorage($this->file);

        self::assertNull($storage->get());

        self::assertFalse(is_file($this->file));
    }

    public function test_clear_removes_file(): void
    {
        $storage = new FileTokenStorage($this->file);

        $storage->store(new Token('abc'));

        self::assertFileExists($this->file);

        $storage->clear();

        self::assertFileDoesNotExist($this->file);
    }
}
