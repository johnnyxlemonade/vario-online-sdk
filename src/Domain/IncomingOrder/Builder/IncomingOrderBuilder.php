<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Builder;

use DateTimeImmutable;
use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\IncomingOrder\Calculator\IncomingOrderLineCalculator;
use Lemonade\Vario\Domain\IncomingOrder\Calculator\IncomingOrderTotalsCalculator;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPaymentMeansCode;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderTaxExchangeRate;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderCalculatedLineInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderInput;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;

/**
 * Class IncomingOrderBuilder
 *
 * Developer-friendly builder that creates a complete IncomingOrderInput
 * from simplified line definitions and automatically calculated totals.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder\Builder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderBuilder
{
    private IncomingOrderLineCalculator $lineCalculator;
    private IncomingOrderTotalsCalculator $totalsCalculator;

    public function __construct(
        ?IncomingOrderLineCalculator $lineCalculator = null,
        ?IncomingOrderTotalsCalculator $totalsCalculator = null,
    ) {
        $this->lineCalculator = $lineCalculator ?? new IncomingOrderLineCalculator();
        $this->totalsCalculator = $totalsCalculator ?? new IncomingOrderTotalsCalculator();
    }

    /**
     * @param list<IncomingOrderCalculatedLineInput> $lines
     */
    public function build(
        string $uuid,
        DateTimeImmutable $issueDate,
        Currency $currency,
        KnownPartyInput $buyerCustomerParty,
        KnownPartyInput $sellerSupplierParty,
        array $lines,
        ?string $id = null,
        ?KnownPartyInput $accountingCustomerParty = null,
        ?KnownPartyInput $delivery = null,
        ?string $note = null,
        bool $partialDeliveryIndicator = false,
        ?IncomingOrderPaymentMeansCode $paymentMeansCode = null,
        ?IncomingOrderTaxExchangeRate $taxExchangeRate = null,
    ): IncomingOrderInput {
        $documentLines = [];

        foreach ($lines as $line) {
            $documentLines[] = $this->lineCalculator->calculate($line);
        }

        $totals = $this->totalsCalculator->calculate($documentLines);

        if ($taxExchangeRate === null) {
            $taxExchangeRate = new IncomingOrderTaxExchangeRate(
                taxCurrency: $currency,
                referenceCurrencyRate: 1.0,
                taxCurrencyRate: 1.0,
                rateDate: $issueDate,
                exchangeMarketBic: null,
            );
        }

        return (new IncomingOrderInput(
            uuid: $uuid,
            issueDate: $issueDate,
            currency: $currency,
            buyerCustomerParty: $buyerCustomerParty,
            sellerSupplierParty: $sellerSupplierParty,
            monetaryTotal: $totals['monetaryTotal'],
            taxExchangeRate: $taxExchangeRate,
            taxTotal: $totals['taxTotal'],
        ))
            ->withId($id)
            ->withAccountingCustomerParty($accountingCustomerParty)
            ->withDelivery($delivery)
            ->withNote($note)
            ->withPartialDeliveryIndicator($partialDeliveryIndicator)
            ->withPaymentMeansCode($paymentMeansCode)
            ->withDocumentLines($documentLines);
    }
}
