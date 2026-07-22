<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Builder;

use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineInput;
use Lemonade\Vario\Domain\Shared\Document\Calculator\DocumentLineCalculator;
use Lemonade\Vario\Domain\Shared\Document\Calculator\DocumentTotalsCalculator;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxExchangeRate;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentIdentityInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentTotalsInput;

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
            identity: new DocumentIdentityInput(
                uuid: $identity->getUuid(),
                id: $identity->getId(),
                note: $identity->getNote(),
            ),
            issueDate: $issueDate,
            currency: $currency,
            parties: $parties,
            totals: new DocumentTotalsInput(
                monetaryTotal: $totals['monetaryTotal'],
                taxExchangeRate: $taxExchangeRate,
                taxTotal: $totals['taxTotal'],
            ),
            partialDeliveryIndicator: $input->isPartialDeliveryIndicator(),
            paymentMeansCode: $input->getPaymentMeansCode(),
        ))
            ->withDocumentLines($documentLines);
    }
}
