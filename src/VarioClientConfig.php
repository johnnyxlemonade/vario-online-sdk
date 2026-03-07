<?php

declare(strict_types=1);

namespace Lemonade\Vario;

use Lemonade\Vario\Exception\ConfigurationException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class VarioClientConfig
 *
 * Immutable configuration container for the Vario API client.
 *
 * This object encapsulates all parameters required to initialize
 * the SDK including connection settings, authentication credentials
 * and logging configuration.
 *
 * The configuration instance is typically passed to:
 *
 *     VarioApiFactory::create()
 *
 * which uses it to bootstrap the HTTP client, authentication
 * subsystem and API modules.
 *
 * A helper factory `fromEnv()` is provided for loading configuration
 * from environment variables, making the SDK easier to integrate
 * into containerized or cloud-based deployments.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario
 * @category    Configuration
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrák
 * @license     MIT
 * @since       1.0
 */
final class VarioClientConfig
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly string $baseUrl,
        private readonly string $loginName,
        private readonly string $password,
        private readonly string $companyNumber,
        private readonly int $timeout = 30,
        private readonly bool $verifySsl = false,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getLoginName(): string
    {
        return $this->loginName;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCompanyNumber(): string
    {
        return $this->companyNumber;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function isVerifySsl(): bool
    {
        return $this->verifySsl;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public static function fromEnv(): self
    {
        return new self(
            baseUrl: self::env('VARIO_URL'),
            loginName: self::env('VARIO_LOGIN'),
            password: self::env('VARIO_PASSWORD'),
            companyNumber: self::env('VARIO_COMPANY'),
        );
    }

    private static function env(string $key): string
    {
        $value = $_ENV[$key] ?? getenv($key);

        if (!is_string($value) || $value === '') {
            throw new ConfigurationException(
                sprintf('Missing environment variable "%s".', $key)
            );
        }

        return $value;
    }
}
