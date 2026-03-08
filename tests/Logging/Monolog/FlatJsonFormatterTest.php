<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Logging\Monolog;

use DateTimeImmutable;
use Lemonade\Vario\Logging\Monolog\FlatJsonFormatter;
use Monolog\Level;
use Monolog\LogRecord;
use PHPUnit\Framework\TestCase;

final class FlatJsonFormatterTest extends TestCase
{
    public function test_format_creates_flat_json(): void
    {
        $formatter = new FlatJsonFormatter();

        $record = new LogRecord(
            datetime: new DateTimeImmutable('2024-01-01T10:00:00+00:00'),
            channel: 'test',
            level: Level::Info,
            message: 'Hello world',
            context: ['userId' => 123],
            extra: ['requestId' => 'abc']
        );

        $json = $formatter->format($record);

        $data = json_decode($json, true);

        self::assertIsArray($data);

        /** @var array<string,mixed> $data */

        self::assertSame('2024-01-01T10:00:00+00:00', $data['timestamp']);
        self::assertSame('INFO', $data['level']);
        self::assertSame('test', $data['channel']);
        self::assertSame('Hello world', $data['message']);

        self::assertSame(123, $data['userId']);
        self::assertSame('abc', $data['requestId']);
    }

    public function test_format_batch(): void
    {
        $formatter = new FlatJsonFormatter();

        $records = [
            new LogRecord(
                datetime: new DateTimeImmutable(),
                channel: 'test',
                level: Level::Info,
                message: 'first',
                context: [],
                extra: []
            ),
            new LogRecord(
                datetime: new DateTimeImmutable(),
                channel: 'test',
                level: Level::Info,
                message: 'second',
                context: [],
                extra: []
            ),
        ];

        $output = $formatter->formatBatch($records);

        $lines = array_filter(
            explode("\n", $output),
            static fn(string $line): bool => $line !== ''
        );

        self::assertCount(2, $lines);
    }
}
