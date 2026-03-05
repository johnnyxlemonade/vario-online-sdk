<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests;

use Lemonade\Vario\Api\AbstractApi;
use Lemonade\Vario\Client\VarioClientInterface;
use Lemonade\Vario\VarioApi;
use PHPUnit\Framework\TestCase;

final class VarioApiTest extends TestCase
{
    public function test_api_is_created_from_factory(): void
    {
        $client = $this->createMock(VarioClientInterface::class);

        $api = new class ($client) extends AbstractApi {};

        $vario = new VarioApi(
            $client,
            [
                $api::class => fn() => $api,
            ]
        );

        $result = $vario->api($api::class);

        self::assertSame($api, $result);
    }

    public function test_api_is_cached(): void
    {
        $client = $this->createMock(VarioClientInterface::class);

        $factoryCalls = 0;

        $vario = new VarioApi(
            $client,
            [
                AbstractApi::class => function () use (&$factoryCalls, $client) {
                    $factoryCalls++;
                    return new class ($client) extends AbstractApi {};
                },
            ]
        );

        $a = $vario->api(AbstractApi::class);
        $b = $vario->api(AbstractApi::class);

        self::assertSame($a, $b);
        self::assertSame(1, $factoryCalls);
    }

    public function test_missing_factory_throws_exception(): void
    {
        $client = $this->createMock(VarioClientInterface::class);

        $vario = new VarioApi($client, []);

        $this->expectException(\LogicException::class);

        $vario->api(AbstractApi::class);
    }

    public function test_client_is_exposed(): void
    {
        $client = $this->createMock(VarioClientInterface::class);

        $vario = new VarioApi($client, []);

        self::assertSame($client, $vario->client());
    }
}
