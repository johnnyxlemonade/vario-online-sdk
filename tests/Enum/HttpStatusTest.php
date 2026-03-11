<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Enum;

use Lemonade\Vario\Enum\HttpStatus;
use PHPUnit\Framework\TestCase;

final class HttpStatusTest extends TestCase
{
    public function testEnumValues(): void
    {
        self::assertSame(200, HttpStatus::OK->value);
        self::assertSame(401, HttpStatus::UNAUTHORIZED->value);
        self::assertSame(403, HttpStatus::FORBIDDEN->value);
        self::assertSame(429, HttpStatus::TOO_MANY_REQUESTS->value);
        self::assertSame(500, HttpStatus::INTERNAL_SERVER_ERROR->value);
    }

    public function testErrorCodeHelpers(): void
    {
        // client error branch
        self::assertTrue(HttpStatus::isClientErrorCode(400));
        self::assertFalse(HttpStatus::isClientErrorCode(200));

        // server error branch
        self::assertTrue(HttpStatus::isServerErrorCode(500));
        self::assertFalse(HttpStatus::isServerErrorCode(400));
    }
}
