<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Auth\Session;

use Lemonade\Vario\Auth\Session\NativePhpSession;
use PHPUnit\Framework\TestCase;

final class NativePhpSessionTest extends TestCase
{
    private NativePhpSession $session;

    protected function setUp(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [];

        $this->session = new NativePhpSession();
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function test_set_and_get(): void
    {
        $this->session->set('foo', 'bar');

        self::assertSame('bar', $this->session->get('foo'));
    }

    public function test_get_returns_null_when_key_missing(): void
    {
        self::assertNull($this->session->get('missing'));
    }

    public function test_has(): void
    {
        $this->session->set('foo', 'bar');

        self::assertTrue($this->session->has('foo'));
        self::assertFalse($this->session->has('missing'));
    }

    public function test_remove(): void
    {
        $this->session->set('foo', 'bar');

        $this->session->remove('foo');

        self::assertFalse($this->session->has('foo'));
    }

    public function test_is_active(): void
    {
        self::assertTrue($this->session->isActive());
    }
}
