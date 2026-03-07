<?php

declare(strict_types=1);

namespace Lemonade\Vario\Http\Adapter;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Lemonade\Vario\VarioClientConfig;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Class GuzzleHttpAdapter
 *
 * Default HTTP adapter implementation based on Guzzle.
 *
 * This adapter provides the PSR-18 HTTP client together with
 * PSR-17 request and stream factories required by the SDK
 * transport layer.
 *
 * The adapter is responsible for configuring the underlying
 * HTTP client using values provided by VarioClientConfig
 * (base URL, timeout, SSL verification and default headers).
 *
 * It can be passed to:
 *
 *     VarioApiFactory::create()
 *
 * allowing the SDK to communicate with the Vario API using
 * the Guzzle HTTP stack.
 *
 * Alternative adapters may be implemented for other HTTP
 * clients (e.g. Symfony HTTP Client) by implementing the
 * HttpAdapterInterface contract.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Http\Adapter
 * @category    Transport
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrák
 * @license     MIT
 * @since       1.0
 */
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
