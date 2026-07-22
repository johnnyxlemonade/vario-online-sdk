<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\OutgoingQuotation\Write;

use DateTimeImmutable;
use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Enum\OutgoingQuotationPaymentMeansCode;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationLineInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationLineItemInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationPartiesInput;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxCalculationMethod;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxScheme;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentMonetaryTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentQuantity;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxExchangeRate;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxSubTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxTotal;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentIdentityInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentLineAmountsInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentLineIdentityInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentTotalsInput;
use PHPUnit\Framework\TestCase;

final class OutgoingQuotationInputTest extends TestCase
{
    public function test_it_delegates_identity_parties_and_totals_getters(): void
    {
        $issueDate = new DateTimeImmutable('2026-06-18T00:00:00+02:00');
        $buyer = new KnownPartyInput('Buyer');
        $seller = new KnownPartyInput('Seller');
        $monetaryTotal = new DocumentMonetaryTotal(
            payableAmount: 121.0,
            payableRoundingAmount: 0.0,
            taxExclusiveAmount: 100.0,
            taxInclusiveAmount: 121.0,
        );
        $taxExchangeRate = new DocumentTaxExchangeRate(
            taxCurrency: Currency::CZK,
            referenceCurrencyRate: 1.0,
            taxCurrencyRate: 1.0,
            rateDate: $issueDate,
        );
        $taxTotal = new DocumentTaxTotal(
            taxAmount: 21.0,
            taxSubTotals: [
                new DocumentTaxSubTotal(
                    calculationMethod: DocumentTaxCalculationMethod::Total,
                    scheme: DocumentTaxScheme::Vat,
                    taxableAmount: 100.0,
                    taxAmount: 21.0,
                    taxPercentage: 21.0,
                ),
            ],
        );
        $line = new OutgoingQuotationLineInput(
            identity: new DocumentLineIdentityInput(
                uuid: 'line-1',
            ),
            amounts: new DocumentLineAmountsInput(
                lineExtensionAmount: 100.0,
                lineExtensionAmountTaxInclusive: 121.0,
            ),
            lineItem: new OutgoingQuotationLineItemInput(),
            lineQuantity: new DocumentQuantity(
                value: 1.0,
                unitCode: 'Ks',
                scheme: DocumentUnitOfMeasureScheme::Unknown,
            ),
            taxSubTotal: new DocumentTaxSubTotal(
                calculationMethod: DocumentTaxCalculationMethod::Add,
                scheme: DocumentTaxScheme::Vat,
                taxableAmount: 100.0,
                taxAmount: 21.0,
                taxPercentage: 21.0,
            ),
        );

        $input = new OutgoingQuotationInput(
            identity: new DocumentIdentityInput(
                uuid: 'quotation-uuid-1',
                id: 'QUO-001',
                note: 'Header note',
            ),
            issueDate: $issueDate,
            currency: Currency::CZK,
            parties: new OutgoingQuotationPartiesInput(
                buyerCustomerParty: $buyer,
                sellerSupplierParty: $seller,
            ),
            totals: new DocumentTotalsInput(
                monetaryTotal: $monetaryTotal,
                taxExchangeRate: $taxExchangeRate,
                taxTotal: $taxTotal,
            ),
            documentLines: [$line],
            paymentMeansCode: OutgoingQuotationPaymentMeansCode::Cash,
        );

        self::assertSame('quotation-uuid-1', $input->getUuid());
        self::assertSame('QUO-001', $input->getId());
        self::assertSame('Header note', $input->getNote());
        self::assertSame($issueDate, $input->getIssueDate());
        self::assertSame(Currency::CZK, $input->getCurrency());
        self::assertSame($buyer, $input->getBuyerCustomerParty());
        self::assertSame($seller, $input->getSellerSupplierParty());
        self::assertSame($monetaryTotal, $input->getMonetaryTotal());
        self::assertSame($taxExchangeRate, $input->getTaxExchangeRate());
        self::assertSame($taxTotal, $input->getTaxTotal());
        self::assertSame([$line], $input->getDocumentLines());
        self::assertSame(OutgoingQuotationPaymentMeansCode::Cash, $input->getPaymentMeansCode());
    }

    public function test_with_methods_preserve_payload_semantics(): void
    {
        $issueDate1 = new DateTimeImmutable('2026-06-18T00:00:00+02:00');
        $issueDate2 = new DateTimeImmutable('2026-07-01T00:00:00+02:00');
        $buyer1 = new KnownPartyInput('Buyer 1');
        $buyer2 = new KnownPartyInput('Buyer 2');
        $seller1 = new KnownPartyInput('Seller 1');
        $seller2 = new KnownPartyInput('Seller 2');
        $totals1 = new DocumentTotalsInput(
            monetaryTotal: new DocumentMonetaryTotal(
                payableAmount: 121.0,
                payableRoundingAmount: 0.0,
                taxExclusiveAmount: 100.0,
                taxInclusiveAmount: 121.0,
            ),
            taxExchangeRate: new DocumentTaxExchangeRate(
                taxCurrency: Currency::CZK,
                referenceCurrencyRate: 1.0,
                taxCurrencyRate: 1.0,
                rateDate: $issueDate1,
            ),
            taxTotal: new DocumentTaxTotal(
                taxAmount: 21.0,
                taxSubTotals: [
                    new DocumentTaxSubTotal(
                        calculationMethod: DocumentTaxCalculationMethod::Total,
                        scheme: DocumentTaxScheme::Vat,
                        taxableAmount: 100.0,
                        taxAmount: 21.0,
                        taxPercentage: 21.0,
                    ),
                ],
            ),
        );
        $totals2 = new DocumentTotalsInput(
            monetaryTotal: new DocumentMonetaryTotal(
                payableAmount: 242.0,
                payableRoundingAmount: 0.0,
                taxExclusiveAmount: 200.0,
                taxInclusiveAmount: 242.0,
            ),
            taxExchangeRate: new DocumentTaxExchangeRate(
                taxCurrency: Currency::EUR,
                referenceCurrencyRate: 1.0,
                taxCurrencyRate: 25.0,
                rateDate: $issueDate2,
                exchangeMarketBic: 'CNBACZPP',
            ),
            taxTotal: new DocumentTaxTotal(
                taxAmount: 42.0,
                taxSubTotals: [
                    new DocumentTaxSubTotal(
                        calculationMethod: DocumentTaxCalculationMethod::Total,
                        scheme: DocumentTaxScheme::Vat,
                        taxableAmount: 200.0,
                        taxAmount: 42.0,
                        taxPercentage: 21.0,
                    ),
                ],
            ),
        );

        $input = new OutgoingQuotationInput(
            identity: new DocumentIdentityInput(uuid: 'quotation-uuid-1'),
            issueDate: $issueDate1,
            currency: Currency::CZK,
            parties: new OutgoingQuotationPartiesInput(
                buyerCustomerParty: $buyer1,
                sellerSupplierParty: $seller1,
            ),
            totals: $totals1,
            documentLines: [],
        );

        $result = $input
            ->withUuid('quotation-uuid-2')
            ->withId('QUO-002')
            ->withNote('Updated note')
            ->withIssueDate($issueDate2)
            ->withCurrency(Currency::EUR)
            ->withBuyerCustomerParty($buyer2)
            ->withSellerSupplierParty($seller2)
            ->withMonetaryTotal($totals2->getMonetaryTotal())
            ->withTaxExchangeRate($totals2->getTaxExchangeRate())
            ->withTaxTotal($totals2->getTaxTotal())
            ->withPaymentMeansCode(OutgoingQuotationPaymentMeansCode::BankAccount);

        self::assertNotSame($input, $result);
        self::assertSame('quotation-uuid-2', $result->getUuid());
        self::assertSame('QUO-002', $result->getId());
        self::assertSame('Updated note', $result->getNote());
        self::assertSame($issueDate2, $result->getIssueDate());
        self::assertSame(Currency::EUR, $result->getCurrency());
        self::assertSame($buyer2, $result->getBuyerCustomerParty());
        self::assertSame($seller2, $result->getSellerSupplierParty());
        self::assertSame($totals2->getMonetaryTotal(), $result->getMonetaryTotal());
        self::assertSame($totals2->getTaxExchangeRate(), $result->getTaxExchangeRate());
        self::assertSame($totals2->getTaxTotal(), $result->getTaxTotal());
        self::assertSame(OutgoingQuotationPaymentMeansCode::BankAccount, $result->getPaymentMeansCode());
    }
}
