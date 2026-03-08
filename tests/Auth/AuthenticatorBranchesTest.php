<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Auth;

use Lemonade\Vario\Auth\Authenticator;
use Lemonade\Vario\Auth\Storage\TokenStorageInterface;
use Lemonade\Vario\Auth\Token;
use Lemonade\Vario\Exception\AuthenticationException;
use Lemonade\Vario\VarioClientConfig;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;

final class AuthenticatorBranchesTest extends TestCase
{
    private function createConfig(LoggerInterface $logger): VarioClientConfig
    {
        return new VarioClientConfig(
            baseUrl: 'https://example.test',
            loginName: 'user',
            password: 'pass',
            companyNumber: '123',
            logger: $logger
        );
    }

    public function test_authentication_is_skipped_when_token_exists(): void
    {
        $http = $this->createMock(ClientInterface::class);
        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $storage = $this->createMock(TokenStorageInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $storage->method('get')->willReturn(new Token('existing'));

        $logger
            ->expects(self::once())
            ->method('debug')
            ->with('Vario authentication skipped: valid token already present');

        $config = $this->createConfig($logger);

        $auth = new Authenticator(
            $http,
            $requestFactory,
            $streamFactory,
            $storage,
            $config
        );

        $auth->authenticate();
    }

    public function test_authentication_fails_when_response_token_is_empty(): void
    {
        $http = $this->createMock(ClientInterface::class);
        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $storage = $this->createMock(TokenStorageInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $storage->method('get')->willReturn(null);

        $config = $this->createConfig($logger);

        $request = $this->createMock(RequestInterface::class);
        $request->method('withHeader')->willReturnSelf();
        $request->method('withBody')->willReturnSelf();

        $requestFactory
            ->method('createRequest')
            ->willReturn($request);

        $stream = $this->createMock(StreamInterface::class);
        $streamFactory->method('createStream')->willReturn($stream);

        $body = $this->createMock(StreamInterface::class);
        $body->method('__toString')->willReturn('');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($body);

        $http->method('sendRequest')->willReturn($response);

        $auth = new Authenticator(
            $http,
            $requestFactory,
            $streamFactory,
            $storage,
            $config
        );

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Authentication failed: Missing token in response');

        $auth->authenticate();
    }
}
