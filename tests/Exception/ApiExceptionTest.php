<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Exception;

use Lemonade\Vario\Exception\ApiException;
use PHPUnit\Framework\TestCase;

final class ApiExceptionTest extends TestCase
{
    public function test_exception_stores_message_and_metadata(): void
    {
        $exception = new ApiException(
            message: 'API request failed',
            statusCode: 500,
            responseBody: '{"error":"internal"}'
        );

        self::assertSame('API request failed', $exception->getMessage());
        self::assertSame(500, $exception->getStatusCode());
        self::assertSame('{"error":"internal"}', $exception->getResponseBody());

        // code je vždy 0 (viz konstruktor)
        self::assertSame(0, $exception->getCode());
    }

    public function test_exception_accepts_previous_exception(): void
    {
        $previous = new \RuntimeException('transport error');

        $exception = new ApiException(
            message: 'API request failed',
            statusCode: 400,
            responseBody: 'Bad request',
            previous: $previous
        );

        self::assertSame($previous, $exception->getPrevious());
    }

    public function test_exception_allows_null_metadata(): void
    {
        $exception = new ApiException('API error');

        self::assertNull($exception->getStatusCode());
        self::assertNull($exception->getResponseBody());
    }
}
