<?php declare(strict_types=1);

namespace Lemonade\Vario\Tests\Http\Adapter;

use Lemonade\Vario\Http\Adapter\HttpAdapterInterface;
use Lemonade\Vario\VarioApiFactory;
use Lemonade\Vario\VarioClientConfig;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\HttpFactory;

final class CustomHttpAdapterTest extends TestCase
{
    public function test_custom_adapter_is_supported(): void
    {
        $config = new VarioClientConfig(
            baseUrl: 'https://example.com',
            loginName: 'user',
            password: 'pass',
            companyNumber: '1'
        );

        $adapter = new class implements HttpAdapterInterface {

            private HttpFactory $factory;

            public function __construct()
            {
                $this->factory = new HttpFactory();
            }

            public function client(): ClientInterface
            {
                return new class implements ClientInterface {

                    public function sendRequest(RequestInterface $request): ResponseInterface
                    {
                        return new Response(
                            200,
                            [],
                            json_encode(['AccessToken' => 'fake'])
                        );
                    }
                };
            }

            public function requestFactory(): RequestFactoryInterface
            {
                return $this->factory;
            }

            public function streamFactory(): StreamFactoryInterface
            {
                return $this->factory;
            }
        };

        $vario = VarioApiFactory::create($config, $adapter);

        self::assertNotNull($vario);
    }
}
