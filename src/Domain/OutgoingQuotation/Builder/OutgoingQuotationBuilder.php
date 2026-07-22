<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\OutgoingQuotation\Builder;

use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationLineInput;
use Lemonade\Vario\Domain\Shared\Document\Calculator\DocumentLineCalculator;
use Lemonade\Vario\Domain\Shared\Document\Calculator\DocumentTotalsCalculator;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxCalculationMethod;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxExchangeRate;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxSubTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxTotal;

final class OutgoingQuotationBuilder
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

    public function build(OutgoingQuotationBuildInput $input): OutgoingQuotationInput
    {
        $documentLines = [];
        $lines = $input->getLines();
        $currency = $input->getCurrency();
        $issueDate = $input->getIssueDate();
        $taxExchangeRate = $input->getTaxExchangeRate();
        $identity = $input->getIdentity();
        $parties = $input->getParties();

        foreach ($lines as $line) {
            $calculated = $this->lineCalculator->calculate($line);

            $documentLines[] = new OutgoingQuotationLineInput(
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

        $totals = $this->totalsCalculator->calculate($documentLines, $input->getPayableRoundingAmount());
        $taxTotal = new DocumentTaxTotal(
            taxAmount: $totals['taxTotal']->getTaxAmount(),
            taxSubTotals: array_map(
                static fn(DocumentTaxSubTotal $taxSubTotal): DocumentTaxSubTotal => new DocumentTaxSubTotal(
                    calculationMethod: DocumentTaxCalculationMethod::Total,
                    scheme: $taxSubTotal->getScheme(),
                    taxableAmount: $taxSubTotal->getTaxableAmount(),
                    taxAmount: $taxSubTotal->getTaxAmount(),
                    taxPercentage: $taxSubTotal->getTaxPercentage(),
                    taxSchemeExtensionCode: $taxSubTotal->getTaxSchemeExtensionCode(),
                ),
                $totals['taxTotal']->getTaxSubTotals(),
            ),
        );

        if ($taxExchangeRate === null) {
            $taxExchangeRate = new DocumentTaxExchangeRate(
                taxCurrency: $currency,
                referenceCurrencyRate: 1.0,
                taxCurrencyRate: 1.0,
            );
        }

        return new OutgoingQuotationInput(
            uuid: $identity->getUuid(),
            issueDate: $issueDate,
            currency: $currency,
            buyerCustomerParty: $parties->getBuyerCustomerParty(),
            sellerSupplierParty: $parties->getSellerSupplierParty(),
            monetaryTotal: $totals['monetaryTotal'],
            taxExchangeRate: $taxExchangeRate,
            taxTotal: $taxTotal,
            documentLines: $documentLines,
            id: $identity->getId(),
            note: $identity->getNote(),
            paymentMeansCode: $input->getPaymentMeansCode(),
        );
    }
}
