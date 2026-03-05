<?php declare(strict_types=1);

namespace Lemonade\Vario\Logging\Monolog;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\LogRecord;

/**
 * Class HybridRotatingFileHandler
 *
 * Custom Monolog handler providing hybrid log rotation based on
 * date and file size.
 *
 * The handler writes logs into daily log files using the pattern:
 *
 *     {channel}_YYYY-MM-DD.log
 *
 * Once the file reaches the configured size limit, it is rotated
 * by appending an incremental numeric suffix:
 *
 *     {channel}_YYYY-MM-DD.log
 *     {channel}_YYYY-MM-DD_1.log
 *     {channel}_YYYY-MM-DD_2.log
 *
 * This approach combines predictable daily log grouping with
 * size-based rotation to prevent uncontrolled file growth.
 *
 * Designed for structured logging environments (e.g. JSON logs)
 * where external log processors such as ELK, OpenSearch or Loki
 * consume log files.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Logging\Monolog
 * @category    Logging
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
class HybridRotatingFileHandler extends StreamHandler
{
    private string $currentLogPath;

    public function __construct(
        private readonly string $logDirectory,
        private readonly string $channel,
        private readonly int $maxFileSizeBytes = 10485760, // 10MB
        Level|string|int $level = Level::Debug,
        bool $bubble = true
    ) {
        $date = date('Y-m-d');

        $baseLogPath = rtrim($this->logDirectory, '/\\')
            . DIRECTORY_SEPARATOR
            . $this->channel . '_' . $date . '.log';

        $this->currentLogPath = $baseLogPath;

        parent::__construct($this->currentLogPath, $level, $bubble);
    }

    protected function write(LogRecord $record): void
    {
        if ($this->isRotationNeeded()) {
            $this->rotate();
        }

        parent::write($record);
    }

    private function isRotationNeeded(): bool
    {
        if (!file_exists($this->currentLogPath)) {
            return false;
        }

        return filesize($this->currentLogPath) >= $this->maxFileSizeBytes;
    }

    private function rotate(): void
    {
        $suffix = 1;

        do {
            $rotatedPath = $this->getRotatedFilePath($suffix++);
        } while (file_exists($rotatedPath));

        rename($this->currentLogPath, $rotatedPath);

        if (is_resource($this->stream)) {
            fclose($this->stream);
        }

        $this->stream = null;
    }

    private function getRotatedFilePath(int $suffix): string
    {
        $date = date('Y-m-d');

        $base = rtrim($this->logDirectory, '/\\')
            . DIRECTORY_SEPARATOR
            . $this->channel . '_' . $date . '.log';

        return str_replace('.log', "_{$suffix}.log", $base);
    }
}