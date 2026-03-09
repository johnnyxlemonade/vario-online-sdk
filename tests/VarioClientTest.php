<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Client;

use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Lemonade\Vario\Auth\Storage\InMemoryTokenStorage;
use Lemonade\Vario\Auth\Token;
use Lemonade\Vario\Client\VarioClient;
use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Exception\ApiException;
use Lemonade\Vario\Exception\AuthenticationException;
use Lemonade\Vario\Exception\ForbiddenException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Log\NullLogger;

final class VarioClientTest extends TestCase
{
    public function test_send_json_success(): void
    {
        $http = $this->createMock(ClientInterface::class);

        $http->method('sendRequest')
            ->willReturn(
                new Response(
                    200,
                    [],
                    '{"ok":true}'
                )
            );

        $client = new VarioClient(
            httpClient: $http,
            tokenStorage: new InMemoryTokenStorage(),
            requestFactory: new HttpFactory(),
            logger: new NullLogger(),
            reauthCallback: fn() => null,
        );

        $result = $client->sendJson(HttpMethod::GET, '/test');

        self::assertTrue($result['ok']);
    }

    public function test_retry_on_401(): void
    {
        $http = $this->createMock(ClientInterface::class);

        $http->expects(self::exactly(2))
            ->method('sendRequest')
            ->willReturnOnConsecutiveCalls(
                new Response(401),
                new Response(200, [], '{"ok":true}')
            );

        $reauthCalled = false;

        $client = new VarioClient(
            httpClient: $http,
            tokenStorage: new InMemoryTokenStorage(),
            requestFactory: new HttpFactory(),
            logger: new NullLogger(),
            reauthCallback: function () use (&$reauthCalled) {
                $reauthCalled = true;
            },
        );

        $result = $client->sendJson(HttpMethod::GET, '/test');

        self::assertTrue($reauthCalled);
        self::assertTrue($result['ok']);
    }

    public function test_forbidden_exception(): void
    {
        $http = $this->createMock(ClientInterface::class);

        $http->method('sendRequest')
            ->willReturn(
                new Response(403, [], 'forbidden')
            );

        $client = new VarioClient(
            httpClient: $http,
            tokenStorage: new InMemoryTokenStorage(),
            requestFactory: new HttpFactory(),
            logger: new NullLogger(),
            reauthCallback: fn() => null,
        );

        $this->expectException(ForbiddenException::class);

        $client->sendJson(HttpMethod::GET, '/test');
    }

    public function test_send_query_builds_query_string(): void
    {
        $http = $this->createMock(ClientInterface::class);

        $http->method('sendRequest')
            ->willReturn(new Response(200, [], '{"ok":true}'));

        $client = new VarioClient(
            httpClient: $http,
            tokenStorage: new InMemoryTokenStorage(),
            requestFactory: new HttpFactory(),
            logger: new NullLogger(),
            reauthCallback: fn() => null,
        );

        $result = $client->sendQuery(
            HttpMethod::GET,
            '/test',
            ['page' => 1]
        );

        self::assertTrue($result['ok']);
    }

    public function test_empty_response_returns_empty_array(): void
    {
        $http = $this->createMock(ClientInterface::class);

        $http->method('sendRequest')
            ->willReturn(new Response(200));

        $client = new VarioClient(
            httpClient: $http,
            tokenStorage: new InMemoryTokenStorage(),
            requestFactory: new HttpFactory(),
            logger: new NullLogger(),
            reauthCallback: fn() => null,
        );

        $result = $client->sendJson(HttpMethod::GET, '/test');

        self::assertSame([], $result);
    }

    public function test_api_exception_on_server_error(): void
    {
        $http = $this->createMock(ClientInterface::class);

        $http->method('sendRequest')
            ->willReturn(new Response(500, [], 'error'));

        $client = new VarioClient(
            httpClient: $http,
            tokenStorage: new InMemoryTokenStorage(),
            requestFactory: new HttpFactory(),
            logger: new NullLogger(),
            reauthCallback: fn() => null,
        );

        $this->expectException(ApiException::class);

        $client->sendJson(HttpMethod::GET, '/test');
    }

    public function test_authorization_header_is_added(): void
    {
        $http = $this->createMock(ClientInterface::class);

        $http->method('sendRequest')
            ->willReturn(new Response(200, [], '{"ok":true}'));

        $storage = new InMemoryTokenStorage();
        $storage->store(new Token('abc'));

        $client = new VarioClient(
            httpClient: $http,
            tokenStorage: $storage,
            requestFactory: new HttpFactory(),
            logger: new NullLogger(),
            reauthCallback: fn() => null,
        );

        $result = $client->sendJson(HttpMethod::GET, '/test');

        self::assertTrue($result['ok']);
    }

    public function test_reauthenticate_callback_is_called(): void
    {
        $http = $this->createMock(ClientInterface::class);

        $http->method('sendRequest')
            ->willReturn(new Response(401));

        $reauthCalled = false;

        $client = new VarioClient(
            httpClient: $http,
            tokenStorage: new InMemoryTokenStorage(),
            requestFactory: new HttpFactory(),
            logger: new NullLogger(),
            reauthCallback: function () use (&$reauthCalled) {
                $reauthCalled = true;
            },
        );

        try {
            $client->sendJson(HttpMethod::GET, '/test');
        } catch (\Throwable) {
            // ignore
        }

        self::assertTrue($reauthCalled);
    }

    public function test_reauthenticate_exception_is_wrapped(): void
    {
        $http = $this->createMock(ClientInterface::class);

        $http->method('sendRequest')
            ->willReturn(new Response(401));

        $client = new VarioClient(
            httpClient: $http,
            tokenStorage: new InMemoryTokenStorage(),
            requestFactory: new HttpFactory(),
            logger: new NullLogger(),
            reauthCallback: function () {
                throw new \RuntimeException('auth failed');
            },
        );

        $this->expectException(AuthenticationException::class);

        $client->sendJson(HttpMethod::GET, '/test');
    }

    public function test_http_client_exception_is_wrapped_into_api_exception(): void
    {
        $http = $this->createMock(ClientInterface::class);

        $http->method('sendRequest')
            ->willThrowException(
                new class ('HTTP error') extends \RuntimeException implements ClientExceptionInterface {}
            );

        $client = new VarioClient(
            httpClient: $http,
            tokenStorage: new InMemoryTokenStorage(),
            requestFactory: new HttpFactory(),
            logger: new NullLogger(),
            reauthCallback: fn() => null,
        );

        $this->expectException(ApiException::class);

        $client->sendJson(HttpMethod::GET, '/test');
    }

    public function test_invalid_json_structure_throws_api_exception(): void
    {
        $http = $this->createMock(ClientInterface::class);

        $http->method('sendRequest')
            ->willReturn(
                new Response(
                    200,
                    [],
                    'true'   // valid JSON, ale není array
                )
            );

        $client = new VarioClient(
            httpClient: $http,
            tokenStorage: new InMemoryTokenStorage(),
            requestFactory: new HttpFactory(),
            logger: new NullLogger(),
            reauthCallback: fn() => null,
        );

        $this->expectException(ApiException::class);

        $client->sendJson(HttpMethod::GET, '/test');
    }

    public function test_send_json_writes_payload_to_body(): void
    {
        /** @var ClientInterface&MockObject $http */
        $http = $this->createMock(ClientInterface::class);

        /** @var RequestInterface|null $capturedRequest */
        $capturedRequest = null;

        $http->expects(self::once())
            ->method('sendRequest')
            ->willReturnCallback(function (RequestInterface $request) use (&$capturedRequest) {
                $capturedRequest = $request;

                return new Response(
                    200,
                    [],
                    '{"ok":true}'
                );
            });

        $client = new VarioClient(
            httpClient: $http,
            tokenStorage: new InMemoryTokenStorage(),
            requestFactory: new HttpFactory(),
            logger: new NullLogger(),
            reauthCallback: fn() => null,
        );

        $client->sendJson(
            HttpMethod::POST,
            '/test',
            ['a' => 1]
        );

        self::assertNotNull($capturedRequest);

        $body = (string) $capturedRequest->getBody();

        self::assertSame('{"a":1}', $body);
    }

    public function test_prepare_request_removes_existing_authorization_header(): void
    {
        /** @var ClientInterface&MockObject $http */
        $http = $this->createMock(ClientInterface::class);

        /** @var RequestInterface|null $capturedRequest */
        $capturedRequest = null;

        $http->method('sendRequest')
            ->willReturnCallback(function (RequestInterface $request) use (&$capturedRequest) {
                $capturedRequest = $request;

                return new Response(200, [], '{"ok":true}');
            });

        $client = new VarioClient(
            httpClient: $http,
            tokenStorage: new InMemoryTokenStorage(),
            requestFactory: new HttpFactory(),
            logger: new NullLogger(),
            reauthCallback: fn() => null,
        );

        $factory = new HttpFactory();

        $request = $factory
            ->createRequest('GET', '/test')
            ->withHeader('Authorization', 'Basic something');

        $client->send($request);

        self::assertInstanceOf(RequestInterface::class, $capturedRequest);

        self::assertFalse(
            $capturedRequest->hasHeader('Authorization')
        );
    }
}
