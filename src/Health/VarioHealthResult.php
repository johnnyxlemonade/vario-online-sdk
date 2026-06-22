<?php

declare(strict_types=1);

namespace Lemonade\Vario\Health;

use Throwable;

/**
 * Class VarioHealthResult
 *
 * Immutable value object representing the result of a Vario health check.
 *
 * The result contains the availability flag, failure reason, response
 * status code, elapsed duration and an optional previous exception for
 * debugging purposes.
 *
 * The previous exception is intentionally not included in array output
 * to keep logs and user-facing diagnostics compact and safe.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Health
 * @category    ValueObject
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.12.0
 */
final class VarioHealthResult
{
    private function __construct(
        private readonly bool $available,
        private readonly ?string $reason,
        private readonly ?string $message,
        private readonly float $durationMs,
        private readonly ?int $statusCode = null,
        private readonly ?Throwable $previous = null,
    ) {}

    public static function available(
        float $durationMs,
        ?int $statusCode = null,
    ): self {
        return new self(
            available: true,
            reason: null,
            message: null,
            durationMs: $durationMs,
            statusCode: $statusCode,
        );
    }

    public static function unavailable(
        string $reason,
        string $message,
        float $durationMs,
        ?int $statusCode = null,
        ?Throwable $previous = null,
    ): self {
        return new self(
            available: false,
            reason: $reason,
            message: $message,
            durationMs: $durationMs,
            statusCode: $statusCode,
            previous: $previous,
        );
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function isUnavailable(): bool
    {
        return !$this->available;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getDurationMs(): float
    {
        return $this->durationMs;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getPrevious(): ?Throwable
    {
        return $this->previous;
    }

    public function getSummary(): string
    {
        if ($this->available) {
            return sprintf(
                'Vario server is available%s in %.2f ms.',
                $this->statusCode !== null
                    ? sprintf(' with HTTP %d', $this->statusCode)
                    : '',
                $this->durationMs,
            );
        }

        return sprintf(
            'Vario server is unavailable%s after %.2f ms%s.',
            $this->reason !== null
                ? sprintf(' [%s]', $this->reason)
                : '',
            $this->durationMs,
            $this->message !== null
                ? sprintf(': %s', $this->message)
                : '',
        );
    }

    /**
     * @return array{
     *     available: bool,
     *     reason: string|null,
     *     message: string|null,
     *     duration_ms: float,
     *     status_code: int|null
     * }
     */
    public function toArray(): array
    {
        return [
            'available' => $this->available,
            'reason' => $this->reason,
            'message' => $this->message,
            'duration_ms' => $this->durationMs,
            'status_code' => $this->statusCode,
        ];
    }

    /**
     * @return array{
     *     available: bool,
     *     reason: string|null,
     *     message: string|null,
     *     duration_ms: float,
     *     status_code: int|null
     * }
     */
    public function toLogArray(): array
    {
        return $this->toArray();
    }
}
