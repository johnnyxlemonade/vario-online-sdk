<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Auth\Session;

use Lemonade\Vario\Auth\Session\NativePhpSession;
use PHPUnit\Framework\TestCase;

final class NativePhpSessionTest extends TestCase
{
    private ?NativePhpSession $session = null;

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
        $this->getSession()->set('foo', 'bar');

        self::assertSame('bar', $this->getSession()->get('foo'));
    }

    public function test_get_returns_null_when_key_missing(): void
    {
        self::assertNull($this->getSession()->get('missing'));
    }

    public function test_has(): void
    {
        $this->getSession()->set('foo', 'bar');

        self::assertTrue($this->getSession()->has('foo'));
        self::assertFalse($this->getSession()->has('missing'));
    }

    public function test_remove(): void
    {
        $this->getSession()->set('foo', 'bar');

        $this->getSession()->remove('foo');

        self::assertFalse($this->getSession()->has('foo'));
    }

    public function test_is_active(): void
    {
        self::assertTrue($this->getSession()->isActive());
    }

    private function getSession(): NativePhpSession
    {
        self::assertNotNull($this->session);

        return $this->session;
    }
}
