<?php

declare(strict_types=1);

namespace Lemonade\Vario;

use Lemonade\Vario\Api\AbstractApi;
use Lemonade\Vario\Api\DatasetViewApi;
use Lemonade\Vario\Api\IncomingOrderApi;
use Lemonade\Vario\Api\KnownPartyApi;
use Lemonade\Vario\Api\OutgoingInvoiceApi;
use Lemonade\Vario\Client\VarioClientInterface;

/**
 * Class VarioApi
 *
 * Facade nad Vario Online API.
 * Poskytuje jednotný vstupní bod k jednotlivým API modulům
 * a zapouzdřuje práci s interním HTTP klientem.
 *
 * Jednotlivé API moduly jsou inicializovány lazy
 * (až při prvním použití).
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario
 * @category    Facade
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class VarioApi
{
    /** @var array<class-string<AbstractApi>, callable(): AbstractApi> */
    private array $factories;

    /** @var array<class-string<AbstractApi>, AbstractApi> */
    private array $apis = [];

    /**
     * @param array<class-string<AbstractApi>, callable(): AbstractApi> $factories
     */
    public function __construct(
        private readonly VarioClientInterface $client,
        array $factories
    ) {
        $this->factories = $factories;
    }

    public function client(): VarioClientInterface
    {
        return $this->client;
    }

    /**
     * @template T of AbstractApi
     * @param class-string<T> $class
     * @return T
     */
    public function api(string $class): AbstractApi
    {
        if (!isset($this->apis[$class])) {
            if (!isset($this->factories[$class])) {
                throw new \LogicException(sprintf(
                    'API factory for "%s" not registered.',
                    $class
                ));
            }

            $this->apis[$class] = ($this->factories[$class])();
        }

        /** @var T */
        return $this->apis[$class];
    }


    public function datasetView(): DatasetViewApi
    {
        return $this->api(DatasetViewApi::class);
    }

    public function incomingOrders(): IncomingOrderApi
    {
        return $this->api(IncomingOrderApi::class);
    }

    public function knownParties(): KnownPartyApi
    {
        return $this->api(KnownPartyApi::class);
    }

    public function outgoingInvoices(): OutgoingInvoiceApi
    {
        return $this->api(OutgoingInvoiceApi::class);
    }
}
