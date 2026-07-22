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
    private ?string $logDir = null;

    protected function setUp(): void
    {
        $this->logDir = sys_get_temp_dir() . '/vario_logs_' . uniqid('', true);

        mkdir($this->getLogDir());
    }

    protected function tearDown(): void
    {
        $logDir = $this->logDir;

        if ($logDir === null) {
            return;
        }

        $files = glob($logDir . '/*');

        if (is_array($files)) {
            foreach ($files as $file) {
                unlink($file);
            }
        }

        rmdir($logDir);
    }

    public function test_log_file_is_created(): void
    {
        $handler = new HybridRotatingFileHandler(
            $this->getLogDir(),
            'test',
            1024,
        );

        $record = new LogRecord(
            datetime: new DateTimeImmutable(),
            channel: 'test',
            level: Level::Info,
            message: 'hello world',
            context: [],
            extra: [],
        );

        $handler->handle($record);
        $handler->close();

        $date = date('Y-m-d');

        $file = $this->getLogDir() . "/test_{$date}.log";

        self::assertFileExists($file);
    }

    public function test_log_rotation_when_size_exceeded(): void
    {
        $handler = new HybridRotatingFileHandler(
            $this->getLogDir(),
            'test',
            1, // velmi malĂ˝ limit pro test
        );

        $record = new LogRecord(
            datetime: new DateTimeImmutable(),
            channel: 'test',
            level: Level::Info,
            message: str_repeat('A', 200),
            context: [],
            extra: [],
        );

        $handler->handle($record);
        $handler->handle($record);
        $handler->handle($record);

        $handler->close();

        $date = date('Y-m-d');

        $files = glob($this->getLogDir() . "/test_{$date}*.log");

        self::assertIsArray($files);
        self::assertGreaterThanOrEqual(2, count($files));
    }

    private function getLogDir(): string
    {
        self::assertNotNull($this->logDir);

        return $this->logDir;
    }
}
