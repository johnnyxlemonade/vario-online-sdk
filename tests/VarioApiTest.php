<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests;

use Lemonade\Vario\Api\AbstractApi;
use Lemonade\Vario\Api\DatasetViewApi;
use Lemonade\Vario\Api\IncomingOrderApi;
use Lemonade\Vario\Api\KnownPartyApi;
use Lemonade\Vario\Api\OutgoingInvoiceApi;
use Lemonade\Vario\Client\VarioClientInterface;
use Lemonade\Vario\Mapper\KnownParty\KnownPartyMapper;
use Lemonade\Vario\Normalizer\KnownParty\KnownPartyInputNormalizer;
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

    public function test_dataset_view_facade(): void
    {
        $client = $this->createMock(VarioClientInterface::class);
        $api = new DatasetViewApi($client);

        $vario = new VarioApi(
            $client,
            [
                DatasetViewApi::class => fn() => $api,
            ]
        );

        self::assertSame($api, $vario->datasetView());
    }

    public function test_incoming_orders_facade(): void
    {
        $client = $this->createMock(VarioClientInterface::class);
        $api = new IncomingOrderApi($client);

        $vario = new VarioApi(
            $client,
            [
                IncomingOrderApi::class => fn() => $api,
            ]
        );

        self::assertSame($api, $vario->incomingOrders());
    }

    public function test_known_parties_facade(): void
    {
        $client = $this->createMock(VarioClientInterface::class);
        $api = new KnownPartyApi(
            $client,
            new KnownPartyMapper(),
            new KnownPartyInputNormalizer(),
        );

        $vario = new VarioApi(
            $client,
            [
                KnownPartyApi::class => fn() => $api,
            ]
        );

        self::assertSame($api, $vario->knownParties());
    }

    public function test_outgoing_invoices_facade(): void
    {
        $client = $this->createMock(VarioClientInterface::class);
        $api = new OutgoingInvoiceApi($client);

        $vario = new VarioApi(
            $client,
            [
                OutgoingInvoiceApi::class => fn() => $api,
            ]
        );

        self::assertSame($api, $vario->outgoingInvoices());
    }

}
