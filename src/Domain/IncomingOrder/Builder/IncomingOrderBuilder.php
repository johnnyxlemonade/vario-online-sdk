<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Builder;

use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineInput;
use Lemonade\Vario\Domain\Shared\Document\Calculator\DocumentLineCalculator;
use Lemonade\Vario\Domain\Shared\Document\Calculator\DocumentTotalsCalculator;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxExchangeRate;

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
    private DocumentLineCalculator $lineCalculator;
    private DocumentTotalsCalculator $totalsCalculator;

    public function __construct(
        ?DocumentLineCalculator $lineCalculator = null,
        ?DocumentTotalsCalculator $totalsCalculator = null,
    ) {
        $this->lineCalculator = $lineCalculator ?? new DocumentLineCalculator();
        $this->totalsCalculator = $totalsCalculator ?? new DocumentTotalsCalculator();
    }

    public function build(IncomingOrderBuildInput $input): IncomingOrderInput
    {
        $documentLines = [];
        $lines = $input->getLines();
        $currency = $input->getCurrency();
        $issueDate = $input->getIssueDate();
        $taxExchangeRate = $input->getTaxExchangeRate();
        $parties = $input->getParties();
        $identity = $input->getIdentity();

        foreach ($lines as $line) {
            $calculated = $this->lineCalculator->calculate($line);

            $documentLines[] = new IncomingOrderLineInput(
                uuid: $line->getUuid(),
                lineExtensionAmount: $calculated['lineExtensionAmount'],
                lineExtensionAmountTaxInclusive: $calculated['lineExtensionAmountTaxInclusive'],
                lineItem: $line->getLineItem(),
                lineQuantity: $calculated['lineQuantity'],
                taxSubTotal: $calculated['taxSubTotal'],
                lineAllowanceAmount: $line->getLineAllowanceAmount(),
                id: $line->getId(),
                note: $line->getNote(),
            );
        }

        $totals = $this->totalsCalculator->calculate($documentLines, 0.0);

        if ($taxExchangeRate === null) {
            $taxExchangeRate = new DocumentTaxExchangeRate(
                taxCurrency: $currency,
                referenceCurrencyRate: 1.0,
                taxCurrencyRate: 1.0,
                rateDate: $issueDate,
                exchangeMarketBic: null,
            );
        }

        return (new IncomingOrderInput(
            uuid: $identity->getUuid(),
            issueDate: $issueDate,
            currency: $currency,
            buyerCustomerParty: $parties->getBuyerCustomerParty(),
            sellerSupplierParty: $parties->getSellerSupplierParty(),
            monetaryTotal: $totals['monetaryTotal'],
            taxExchangeRate: $taxExchangeRate,
            taxTotal: $totals['taxTotal'],
        ))
            ->withId($identity->getId())
            ->withAccountingCustomerParty($parties->getAccountingCustomerParty())
            ->withDelivery($parties->getDelivery())
            ->withNote($identity->getNote())
            ->withPartialDeliveryIndicator($input->isPartialDeliveryIndicator())
            ->withPaymentMeansCode($input->getPaymentMeansCode())
            ->withDocumentLines($documentLines);
    }
}
