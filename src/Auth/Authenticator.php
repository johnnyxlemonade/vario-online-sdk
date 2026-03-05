<?php

declare(strict_types=1);

namespace Lemonade\Vario\Auth;

use Lemonade\Vario\Auth\Storage\TokenStorageInterface;
use Lemonade\Vario\Exception\AuthenticationException;
use Lemonade\Vario\VarioClientConfig;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Class Authenticator
 *
 * Handles authentication against the Vario Online API.
 *
 * The authenticator sends an authentication request to obtain
 * an access token and stores the token using a configured
 * TokenStorageInterface implementation.
 *
 * This component is built entirely on PSR standards:
 *
 *  - PSR-7  HTTP messages
 *  - PSR-17 HTTP factories
 *  - PSR-18 HTTP client
 *
 * The class is responsible only for performing the authentication
 * request and persisting the received token. It does not manage
 * token lifecycle outside of the authentication process.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Auth
 * @category    Authentication
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class Authenticator
{
    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly TokenStorageInterface $storage,
        private readonly VarioClientConfig $config
    ) {}

    public function authenticate(): void
    {
        if ($this->storage->get() !== null) {
            $this->config->getLogger()->debug('Vario authentication skipped: valid token already present');
            return;
        }

        try {
            $this->config->getLogger()->info('Vario authentication started');

            $request  = $this->buildRequest();
            $response = $this->httpClient->sendRequest($request);

        } catch (ClientExceptionInterface $e) {
            throw new AuthenticationException(
                'Authentication request failed',
                previous: $e
            );
        }

        $token = trim((string) $response->getBody());

        if ($token === '') {
            throw new AuthenticationException('Authentication failed: Missing token in response');
        }

        $this->storage->store(new Token($token));

        $this->config->getLogger()->info('Vario authentication successful');
    }

    private function buildRequest(): RequestInterface
    {
        $request = $this->requestFactory
            ->createRequest('POST', '/authentication/GetAccessToken')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('X-Requested-With', 'XMLHttpRequest');

        $body = $this->streamFactory->createStream(
            json_encode([
                'LoginName'     => $this->config->getLoginName(),
                'Password'      => $this->config->getPassword(),
                'CompanyNumber' => $this->config->getCompanyNumber(),
            ], JSON_THROW_ON_ERROR)
        );

        return $request->withBody($body);
    }
}
