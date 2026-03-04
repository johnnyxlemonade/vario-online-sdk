<?php declare(strict_types=1);

namespace Lemonade\Vario\Tests;

use Lemonade\Vario\Exception\AuthenticationException;
use Lemonade\Vario\Http\Adapter\HttpAdapterInterface;
use Lemonade\Vario\VarioApiFactory;
use Lemonade\Vario\VarioClientConfig;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class VarioApiFactoryTest extends TestCase
{
    public function test_factory_attempts_authentication(): void
    {
        $config = new VarioClientConfig(
            baseUrl: 'https://example.com',
            loginName: 'test',
            password: 'test',
            companyNumber: '1'
        );

        $adapter = new class implements HttpAdapterInterface {

            public function client(): ClientInterface
            {
                return new \GuzzleHttp\Client();
            }

            public function requestFactory(): RequestFactoryInterface
            {
                return new \GuzzleHttp\Psr7\HttpFactory();
            }

            public function streamFactory(): StreamFactoryInterface
            {
                return new \GuzzleHttp\Psr7\HttpFactory();
            }
        };

        $this->expectException(AuthenticationException::class);

        VarioApiFactory::create($config, $adapter);
    }
}
