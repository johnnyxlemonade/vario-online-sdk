<?php declare(strict_types=1);

namespace Lemonade\Vario\Tests\Http\Adapter;

use Lemonade\Vario\Http\Adapter\GuzzleHttpAdapter;
use Lemonade\Vario\VarioClientConfig;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class GuzzleHttpAdapterTest extends TestCase
{
    public function test_adapter_returns_psr_components(): void
    {
        $config = new VarioClientConfig(
            baseUrl: 'https://example.com',
            loginName: 'user',
            password: 'pass',
            companyNumber: '1'
        );

        $adapter = new GuzzleHttpAdapter($config);

        self::assertInstanceOf(ClientInterface::class, $adapter->client());
        self::assertInstanceOf(RequestFactoryInterface::class, $adapter->requestFactory());
        self::assertInstanceOf(StreamFactoryInterface::class, $adapter->streamFactory());
    }

    public function test_request_and_stream_factory_are_same_instance(): void
    {
        $config = new VarioClientConfig(
            baseUrl: 'https://example.com',
            loginName: 'user',
            password: 'pass',
            companyNumber: '1'
        );

        $adapter = new GuzzleHttpAdapter($config);

        self::assertSame(
            $adapter->requestFactory(),
            $adapter->streamFactory()
        );
    }

    public function test_client_is_reused(): void
    {
        $config = new VarioClientConfig(
            baseUrl: 'https://example.com',
            loginName: 'user',
            password: 'pass',
            companyNumber: '1'
        );

        $adapter = new GuzzleHttpAdapter($config);

        self::assertSame(
            $adapter->client(),
            $adapter->client()
        );
    }
}
