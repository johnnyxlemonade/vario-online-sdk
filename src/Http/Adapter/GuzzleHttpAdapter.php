<?php declare(strict_types=1);

namespace Lemonade\Vario\Http\Adapter;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Lemonade\Vario\VarioClientConfig;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class GuzzleHttpAdapter implements HttpAdapterInterface
{
    private ClientInterface $client;
    private HttpFactory $factory;

    public function __construct(VarioClientConfig $config)
    {
        $this->client = new Client([
            'base_uri' => $config->getBaseUrl(),
            'timeout'  => $config->getTimeout(),
            'verify'   => $config->isVerifySsl(),
            'headers'  => [
                'Accept' => 'application/json',
            ],
        ]);

        $this->factory = new HttpFactory();
    }

    public function client(): ClientInterface
    {
        return $this->client;
    }

    public function requestFactory(): RequestFactoryInterface
    {
        return $this->factory;
    }

    public function streamFactory(): StreamFactoryInterface
    {
        return $this->factory;
    }
}
