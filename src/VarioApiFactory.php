<?php declare(strict_types=1);

namespace Lemonade\Vario;

use Lemonade\Vario\Api\DatasetViewApi;
use Lemonade\Vario\Api\IncomingOrderApi;
use Lemonade\Vario\Api\KnownPartyApi;
use Lemonade\Vario\Api\OutgoingInvoiceApi;
use Lemonade\Vario\Auth\Authenticator;
use Lemonade\Vario\Auth\InMemoryTokenStorage;
use Lemonade\Vario\Auth\TokenStorageInterface;
use Lemonade\Vario\Client\VarioClient;
use Lemonade\Vario\Domain\KnownParty\DefaultKnownPartyFactory;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInputNormalizer;
use Lemonade\Vario\Domain\KnownParty\KnownPartyMapper;
use Lemonade\Vario\Http\Adapter\HttpAdapterInterface;

/**
 * Class VarioApiFactory
 *
 * Factory responsible for creating fully configured VarioApi instance.
 *
 * The HTTP transport layer is not bundled with the SDK.
 * A HttpAdapterInterface implementation must be provided
 * (e.g. GuzzleHttpAdapter, SymfonyHttpAdapter, MockAdapter).
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario
 * @category    Factory
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
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

        $client = new VarioClient(
            httpClient: $httpAdapter->client(),
            tokenStorage: $tokenStorage,
            requestFactory: $httpAdapter->requestFactory(),
            reauthCallback: $authenticator->authenticate(...)
        );

        // fail fast authentication
        $authenticator->authenticate();

        return new VarioApi(
            client: $client,
            factories: [

                DatasetViewApi::class => fn() =>
                new DatasetViewApi($client),

                IncomingOrderApi::class => fn() =>
                new IncomingOrderApi($client),

                OutgoingInvoiceApi::class => fn() =>
                new OutgoingInvoiceApi($client),

                KnownPartyApi::class => fn() =>
                new KnownPartyApi(
                    $client,
                    new KnownPartyMapper(
                        new DefaultKnownPartyFactory()
                    ),
                    new KnownPartyInputNormalizer()
                ),
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
            httpClient: $httpAdapter->client(),
            requestFactory: $httpAdapter->requestFactory(),
            streamFactory: $httpAdapter->streamFactory(),
            storage: $tokenStorage,
            loginName: $config->getLoginName(),
            password: $config->getPassword(),
            companyNumber: $config->getCompanyNumber()
        );
    }

}
