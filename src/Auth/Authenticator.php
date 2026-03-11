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
        $token = $this->storage->get();

        if ($token !== null) {

            if (
                $token->isExpired()
                || !$this->isTokenValidForConfig($token)
            ) {
                $this->config->getLogger()->debug(
                    'Stored token invalid for current configuration, clearing'
                );

                $this->storage->clear();
                $token = null;
            }
        }

        if ($token !== null) {
            $this->config->getLogger()->debug(
                'Vario authentication skipped: valid token already present'
            );
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

        $tokenValue = trim((string) $response->getBody());

        if ($tokenValue === '') {
            throw new AuthenticationException('Authentication failed: Missing token in response');
        }

        $this->storage->store(
            new Token(
                value: $tokenValue,
                expiresAtUtc: null,
                configHash: Token::buildConfigHash($this->config)
            )
        );

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

    private function isTokenValidForConfig(Token $token): bool
    {
        return $token->getConfigHash() === Token::buildConfigHash($this->config);
    }
}
