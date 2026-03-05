<?php declare(strict_types=1);

namespace Lemonade\Vario\Logging\Monolog;

use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;

/**
 * Class FlatJsonFormatter
 *
 * Custom Monolog formatter producing flat JSON log records.
 *
 * The formatter converts Monolog LogRecord objects into a single-line
 * JSON structure suitable for structured logging environments.
 * Context and extra data are merged into the root log object to avoid
 * nested structures and simplify log ingestion.
 *
 * Output fields include:
 *
 *  - timestamp (ISO 8601)
 *  - level
 *  - channel
 *  - message
 *  - context values
 *  - processor extra data
 *
 * Each record is written as a single JSON line, making the output
 * compatible with log aggregation systems such as:
 *
 *  - Elasticsearch / OpenSearch
 *  - Loki
 *  - Datadog
 *  - Graylog
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Logging\Monolog
 * @category    Logging
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class FlatJsonFormatter implements FormatterInterface
{
    public function format(LogRecord $record): string
    {
        $flat = array_merge(
            [
                'timestamp' => $record->datetime->format(DATE_ATOM),
                'level' => $record->level->getName(),
                'channel' => $record->channel,
                'message' => $record->message,
            ],
            $record->context,
            $record->extra
        );

        return json_encode(
                $flat,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            ) . "\n";
    }

    public function formatBatch(array $records): string
    {
        return implode('', array_map([$this, 'format'], $records));
    }
}