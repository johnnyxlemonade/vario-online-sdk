<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Logging\Monolog;

use DateTimeImmutable;
use Lemonade\Vario\Logging\Monolog\HybridRotatingFileHandler;
use Monolog\Level;
use Monolog\LogRecord;
use PHPUnit\Framework\TestCase;

final class HybridRotatingFileHandlerTest extends TestCase
{
    private string $logDir;

    protected function setUp(): void
    {
        $this->logDir = sys_get_temp_dir() . '/vario_logs_' . uniqid('', true);

        mkdir($this->logDir);
    }

    protected function tearDown(): void
    {
        $files = glob($this->logDir . '/*');

        if (is_array($files)) {
            foreach ($files as $file) {
                unlink($file);
            }
        }

        rmdir($this->logDir);
    }

    public function test_log_file_is_created(): void
    {
        $handler = new HybridRotatingFileHandler(
            $this->logDir,
            'test',
            1024
        );

        $record = new LogRecord(
            datetime: new DateTimeImmutable(),
            channel: 'test',
            level: Level::Info,
            message: 'hello world',
            context: [],
            extra: []
        );

        $handler->handle($record);
        $handler->close();

        $date = date('Y-m-d');

        $file = $this->logDir . "/test_{$date}.log";

        self::assertFileExists($file);
    }

    public function test_log_rotation_when_size_exceeded(): void
    {
        $handler = new HybridRotatingFileHandler(
            $this->logDir,
            'test',
            1 // velmi malý limit pro test
        );

        $record = new LogRecord(
            datetime: new DateTimeImmutable(),
            channel: 'test',
            level: Level::Info,
            message: str_repeat('A', 200),
            context: [],
            extra: []
        );

        $handler->handle($record);
        $handler->handle($record);
        $handler->handle($record);

        $handler->close();

        $date = date('Y-m-d');

        $files = glob($this->logDir . "/test_{$date}*.log");

        self::assertIsArray($files);
        self::assertGreaterThanOrEqual(2, count($files));
    }
}
