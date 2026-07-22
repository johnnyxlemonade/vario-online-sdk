<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests;

use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Lemonade\Vario\VarioClientConfig;
use Lemonade\Vario\VarioHealth;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

final class VarioHealthTest extends TestCase
{
    public function testCheckServerReturnsAvailableForSuccessfulResponse(): void
    {
        $client = new FakeHttpClient(new Response(200));
        $health = $this->createHealth($client);

        $result = $health->checkServer();

        self::assertTrue($result->isAvailable());
        self::assertSame(200, $result->getStatusCode());
        self::assertNull($result->getReason());
        self::assertNull($result->getMessage());
        self::assertGreaterThanOrEqual(0.0, $result->getDurationMs());
    }

    public function testCheckServerTreatsClientErrorAsAvailableServerResponse(): void
    {
        $client = new FakeHttpClient(new Response(404));
        $health = $this->createHealth($client);

        $result = $health->checkServer();

        self::assertTrue($result->isAvailable());
        self::assertSame(404, $result->getStatusCode());
    }

    public function testCheckServerTreatsMethodNotAllowedAsAvailableServerResponse(): void
    {
        $client = new FakeHttpClient(new Response(405));
        $health = $this->createHealth($client);

        $result = $health->checkServer();

        self::assertTrue($result->isAvailable());
        self::assertSame(405, $result->getStatusCode());
    }

    public function testCheckServerReturnsUnavailableForServerError(): void
    {
        $client = new FakeHttpClient(new Response(503));
        $health = $this->createHealth($client);

        $result = $health->checkServer();

        self::assertFalse($result->isAvailable());
        self::assertSame('server_error', $result->getReason());
        self::assertSame('Server responded with HTTP 503', $result->getMessage());
        self::assertSame(503, $result->getStatusCode());
    }

    public function testCheckServerReturnsUnavailableForClientException(): void
    {
        $exception = new FakeClientException('Could not connect');
        $client = new FakeHttpClient($exception);
        $health = $this->createHealth($client);

        $result = $health->checkServer();

        self::assertFalse($result->isAvailable());
        self::assertSame(FakeClientException::class, $result->getReason());
        self::assertSame('Could not connect', $result->getMessage());
        self::assertNull($result->getStatusCode());
        self::assertSame($exception, $result->getPrevious());
    }

    public function testCheckServerReturnsUnavailableForGenericThrowable(): void
    {
        $exception = new RuntimeException('Unexpected failure');
        $client = new FakeHttpClient($exception);
        $health = $this->createHealth($client);

        $result = $health->checkServer();

        self::assertFalse($result->isAvailable());
        self::assertSame(RuntimeException::class, $result->getReason());
        self::assertSame('Unexpected failure', $result->getMessage());
        self::assertNull($result->getStatusCode());
        self::assertSame($exception, $result->getPrevious());
    }

    public function testCheckServerBuildsUrlFromConfigBaseUrlAndUri(): void
    {
        $client = new FakeHttpClient(new Response(200));
        $health = $this->createHealth($client);

        $health->checkServer('/authentication/GetAccessToken');

        self::assertNotNull($client->lastRequest);
        self::assertSame(
            'https://vpn.example.test:444/authentication/GetAccessToken',
            (string) $client->lastRequest->getUri(),
        );
    }

    public function testCheckServerNormalizesBaseUrlAndUriSlashes(): void
    {
        $client = new FakeHttpClient(new Response(200));

        $config = new VarioClientConfig(
            baseUrl: 'https://vpn.example.test:444/',
            loginName: 'user',
            password: 'password',
            companyNumber: '1',
        );

        $health = new VarioHealth(
            config: $config,
            httpClient: $client,
            requestFactory: new HttpFactory(),
        );

        $health->checkServer('authentication/GetAccessToken');

        self::assertNotNull($client->lastRequest);
        self::assertSame(
            'https://vpn.example.test:444/authentication/GetAccessToken',
            (string) $client->lastRequest->getUri(),
        );
    }

    public function testCheckServerAddsRequestedWithHeader(): void
    {
        $client = new FakeHttpClient(new Response(200));
        $health = $this->createHealth($client);

        $health->checkServer();

        self::assertNotNull($client->lastRequest);
        self::assertSame(
            'XMLHttpRequest',
            $client->lastRequest->getHeaderLine('X-Requested-With'),
        );
    }

    private function createHealth(FakeHttpClient $client): VarioHealth
    {
        $config = new VarioClientConfig(
            baseUrl: 'https://vpn.example.test:444',
            loginName: 'user',
            password: 'password',
            companyNumber: '1',
        );

        return new VarioHealth(
            config: $config,
            httpClient: $client,
            requestFactory: new HttpFactory(),
        );
    }
}

final class FakeHttpClient implements ClientInterface
{
    public ?RequestInterface $lastRequest = null;

    public function __construct(
        private readonly ResponseInterface|\Throwable $result,
    ) {}

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->lastRequest = $request;

        if ($this->result instanceof \Throwable) {
            throw $this->result;
        }

        return $this->result;
    }
}

final class FakeClientException extends RuntimeException implements ClientExceptionInterface {}
