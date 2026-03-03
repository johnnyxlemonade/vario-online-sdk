<?php declare(strict_types=1);

namespace Lemonade\Vario\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Lemonade\Vario\VarioClientConfig;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class GuzzleFactory
{
    public static function createClient(VarioClientConfig $config): ClientInterface
    {
        return new Client([
            'base_uri' => $config->getBaseUrl(),
            'timeout'  => $config->getTimeout(),
            'verify'   => $config->isVerifySsl(),
            'headers'  => [
                'Accept' => 'application/json',
            ],
        ]);
    }

    public static function createRequestFactory(): RequestFactoryInterface
    {
        return new HttpFactory();
    }

    public static function createStreamFactory(): StreamFactoryInterface
    {
        return new HttpFactory();
    }
}
