<?php declare(strict_types=1);

namespace Lemonade\Vario\Auth;

use Lemonade\Vario\Exception\AuthenticationException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Class Authenticator
 *
 * Zajišťuje autentizaci vůči Vario Online API.
 * Odesílá autentizační request, získává access token
 * a ukládá jej do TokenStorage.
 *
 * Implementace je postavena čistě na PSR rozhraních
 * (PSR-7, PSR-17, PSR-18).
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
        private readonly string $loginName,
        private readonly string $password,
        private readonly string $companyNumber
    ) {}

    /**
     * Provede autentizaci vůči Vario API a uloží access token.
     */
    public function authenticate(): void
    {
        try {
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
    }

    /**
     * Vytvoří PSR-7 request pro získání access tokenu.
     */
    private function buildRequest(): RequestInterface
    {
        $request = $this->requestFactory
            ->createRequest('POST', '/authentication/GetAccessToken')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('X-Requested-With', 'XMLHttpRequest');

        $body = $this->streamFactory->createStream(
            json_encode([
                'LoginName'     => $this->loginName,
                'Password'      => $this->password,
                'CompanyNumber' => $this->companyNumber,
            ], JSON_THROW_ON_ERROR)
        );

        return $request->withBody($body);
    }
}
