<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests;

use Lemonade\Vario\Exception\ConfigurationException;
use Lemonade\Vario\VarioClientConfig;
use PHPUnit\Framework\TestCase;

final class VarioClientConfigTest extends TestCase
{
    public function test_constructor_and_getters(): void
    {
        $config = new VarioClientConfig(
            baseUrl: 'https://example.com',
            loginName: 'user',
            password: 'pass',
            companyNumber: '123',
            timeout: 10,
            verifySsl: true
        );

        self::assertSame('https://example.com', $config->getBaseUrl());
        self::assertSame('user', $config->getLoginName());
        self::assertSame('pass', $config->getPassword());
        self::assertSame('123', $config->getCompanyNumber());
        self::assertSame(10, $config->getTimeout());
        self::assertTrue($config->isVerifySsl());
    }

    public function test_from_env_creates_config(): void
    {
        $_ENV['VARIO_URL'] = 'https://example.com';
        $_ENV['VARIO_LOGIN'] = 'user';
        $_ENV['VARIO_PASSWORD'] = 'pass';
        $_ENV['VARIO_COMPANY'] = '123';

        $config = VarioClientConfig::fromEnv();

        self::assertSame('https://example.com', $config->getBaseUrl());
        self::assertSame('user', $config->getLoginName());
        self::assertSame('pass', $config->getPassword());
        self::assertSame('123', $config->getCompanyNumber());
    }

    public function test_missing_env_variable_throws_exception(): void
    {
        unset(
            $_ENV['VARIO_URL'],
            $_ENV['VARIO_LOGIN'],
            $_ENV['VARIO_PASSWORD'],
            $_ENV['VARIO_COMPANY']
        );

        $this->expectException(ConfigurationException::class);

        VarioClientConfig::fromEnv();
    }
}
