<?php

declare(strict_types=1);

namespace Lemonade\Vario;

use Lemonade\Vario\Api\DatasetViewApi;
use Lemonade\Vario\Api\IncomingOrderApi;
use Lemonade\Vario\Api\KnownPartyApi;
use Lemonade\Vario\Api\OutgoingInvoiceApi;
use Lemonade\Vario\Auth\Authenticator;
use Lemonade\Vario\Auth\Storage\TokenStorageInterface;
use Lemonade\Vario\Client\Http\RequestAuthenticator;
use Lemonade\Vario\Client\Http\RequestLogger;
use Lemonade\Vario\Client\Http\ResponseHandler;
use Lemonade\Vario\Client\VarioClient;
use Lemonade\Vario\Http\Adapter\HttpAdapterInterface;
use Lemonade\Vario\Mapper\KnownParty\KnownPartyMapper;
use Lemonade\Vario\Normalizer\KnownParty\KnownPartyInputNormalizer;

/**
 * Class VarioApiFactory
 *
 * Bootstrap factory responsible for creating a fully configured
 * VarioApi instance.
 *
 * The factory wires together all core SDK components including:
 *
 * - HTTP transport adapter (PSR-18 compatible)
 * - authentication subsystem
 * - token storage implementation
 * - internal API modules
 *
 * The SDK itself does not ship with a built-in HTTP client.
 * Instead, an implementation of HttpAdapterInterface must be
 * provided (e.g. GuzzleHttpAdapter, SymfonyHttpAdapter).
 *
 * During initialization the factory performs a fail-fast
 * authentication to ensure that the client is ready to execute
 * API requests immediately after construction.
 *
 * API modules are registered as lazy factories and instantiated
 * on first access by VarioApi.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario
 * @category    Factory
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrák
 * @license     MIT
 * @since       1.0
 */
final class VarioApiFactory
{
    public static function create(
        VarioClientConfig $config,
        HttpAdapterInterface $httpAdapter,
        TokenStorageInterface $tokenStorage
    ): VarioApi {

        $authenticator = self::createAuthenticator(
            $config,
            $httpAdapter,
            $tokenStorage
        );

        $logger = $config->getLogger();

        $client = new VarioClient(
            httpClient: $httpAdapter->httpClient(),
            tokenStorage: $tokenStorage,
            requestFactory: $httpAdapter->requestFactory(),
            streamFactory: $httpAdapter->streamFactory(),
            logger: $logger,
            requestAuthenticator: new RequestAuthenticator(),
            requestLogger: new RequestLogger(),
            responseHandler: new ResponseHandler($logger),
            reauthCallback: $authenticator->authenticate(...),
        );

        // fail fast authentication
        $authenticator->authenticate();

        return new VarioApi(
            client: $client,
            factories: [
                DatasetViewApi::class => fn() =>
                    new DatasetViewApi($client),
                KnownPartyApi::class => fn() =>
                    new KnownPartyApi(
                        $client,
                        new KnownPartyMapper(),
                        new KnownPartyInputNormalizer(),
                    ),
                IncomingOrderApi::class => fn() =>
                    new IncomingOrderApi($client),
                OutgoingInvoiceApi::class => fn() =>
                    new OutgoingInvoiceApi($client),
            ]
        );
    }

    /**
     * Build authenticator instance.
     */
    private static function createAuthenticator(
        VarioClientConfig $config,
        HttpAdapterInterface $httpAdapter,
        TokenStorageInterface $tokenStorage
    ): Authenticator {
        return new Authenticator(
            httpClient: $httpAdapter->httpClient(),
            requestFactory: $httpAdapter->requestFactory(),
            streamFactory: $httpAdapter->streamFactory(),
            storage: $tokenStorage,
            config: $config
        );
    }

}
