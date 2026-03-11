<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Api;

use Lemonade\Vario\Api\AbstractApi;
use Lemonade\Vario\Client\VarioClientInterface;
use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Enum\VarioEndpoint;
use PHPUnit\Framework\TestCase;

final class AbstractApiTest extends TestCase
{
    private function createApi(VarioClientInterface $client): TestApi
    {
        return new TestApi($client);
    }

    public function test_send_json_delegates_to_client(): void
    {
        $client = $this->createMock(VarioClientInterface::class);

        $client
            ->expects(self::once())
            ->method('sendJson')
            ->with(
                HttpMethod::POST,
                VarioEndpoint::DatasetView->value,
                ['a' => 1]
            )
            ->willReturn(['ok' => true]);

        $api = $this->createApi($client);

        $result = $api->callSendJson(
            HttpMethod::POST,
            VarioEndpoint::DatasetView,
            ['a' => 1]
        );

        self::assertSame(['ok' => true], $result);
    }

    public function test_send_query_delegates_to_client(): void
    {
        $client = $this->createMock(VarioClientInterface::class);

        $client
            ->expects(self::once())
            ->method('sendQuery')
            ->with(
                HttpMethod::GET,
                VarioEndpoint::DatasetView->value,
                ['page' => 1]
            )
            ->willReturn(['data' => []]);

        $api = $this->createApi($client);

        $result = $api->callSendQuery(
            HttpMethod::GET,
            VarioEndpoint::DatasetView,
            ['page' => 1]
        );

        self::assertSame(['data' => []], $result);
    }
}

/**
 * Test helper exposing protected methods of AbstractApi.
 */
final class TestApi extends AbstractApi
{
    /**
     * @param array<string,mixed>|list<mixed>|null $payload
     * @return array<string,mixed>|list<mixed>
     */
    public function callSendJson(
        HttpMethod $method,
        VarioEndpoint $endpoint,
        ?array $payload = null
    ): array {
        return $this->sendJson($method, $endpoint, $payload);
    }

    /**
     * @param array<string,mixed> $query
     * @return array<string,mixed>|list<mixed>
     */
    public function callSendQuery(
        HttpMethod $method,
        VarioEndpoint $endpoint,
        array $query
    ): array {
        return $this->sendQuery($method, $endpoint, $query);
    }
}
