<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Health;

use Lemonade\Vario\Health\VarioHealthResult;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class VarioHealthResultTest extends TestCase
{
    public function testAvailableResult(): void
    {
        $result = VarioHealthResult::available(
            durationMs: 12.34,
            statusCode: 200
        );

        self::assertTrue($result->isAvailable());
        self::assertFalse($result->isUnavailable());
        self::assertNull($result->getReason());
        self::assertNull($result->getMessage());
        self::assertSame(12.34, $result->getDurationMs());
        self::assertSame(200, $result->getStatusCode());
        self::assertNull($result->getPrevious());

        self::assertSame([
            'available' => true,
            'reason' => null,
            'message' => null,
            'duration_ms' => 12.34,
            'status_code' => 200,
        ], $result->toArray());
    }

    public function testUnavailableResult(): void
    {
        $previous = new RuntimeException('Connection failed');

        $result = VarioHealthResult::unavailable(
            reason: RuntimeException::class,
            message: 'Connection failed',
            durationMs: 50.12,
            statusCode: null,
            previous: $previous
        );

        self::assertFalse($result->isAvailable());
        self::assertTrue($result->isUnavailable());
        self::assertSame(RuntimeException::class, $result->getReason());
        self::assertSame('Connection failed', $result->getMessage());
        self::assertSame(50.12, $result->getDurationMs());
        self::assertNull($result->getStatusCode());
        self::assertSame($previous, $result->getPrevious());

        self::assertSame([
            'available' => false,
            'reason' => RuntimeException::class,
            'message' => 'Connection failed',
            'duration_ms' => 50.12,
            'status_code' => null,
        ], $result->toArray());
    }

    public function testPreviousExceptionIsNotIncludedInArrayOutput(): void
    {
        $result = VarioHealthResult::unavailable(
            reason: RuntimeException::class,
            message: 'Connection failed',
            durationMs: 1.0,
            previous: new RuntimeException('Connection failed')
        );

        $array = $result->toArray();

        self::assertArrayNotHasKey('previous', $array);
        self::assertArrayNotHasKey('exception', $array);
        self::assertArrayNotHasKey('trace', $array);
    }

    public function testToLogArrayUsesCompactArrayOutput(): void
    {
        $result = VarioHealthResult::unavailable(
            reason: 'server_error',
            message: 'Server responded with HTTP 500',
            durationMs: 10.0,
            statusCode: 500
        );

        self::assertSame($result->toArray(), $result->toLogArray());
    }

    public function testAvailableSummary(): void
    {
        $result = VarioHealthResult::available(
            durationMs: 8.55,
            statusCode: 204
        );

        self::assertSame(
            'Vario server is available with HTTP 204 in 8.55 ms.',
            $result->getSummary()
        );
    }

    public function testUnavailableSummary(): void
    {
        $result = VarioHealthResult::unavailable(
            reason: 'server_error',
            message: 'Server responded with HTTP 500',
            durationMs: 20.25,
            statusCode: 500
        );

        self::assertSame(
            'Vario server is unavailable [server_error] after 20.25 ms: Server responded with HTTP 500.',
            $result->getSummary()
        );
    }
}
