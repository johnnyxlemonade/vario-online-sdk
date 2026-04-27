<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Write;

use DateTimeImmutable;
use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPaymentMeansCode;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTaxCalculationMethod;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTaxScheme;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderUnitOfMeasureScheme;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderMonetaryTotal;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderQuantity;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderTaxExchangeRate;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderTaxSubTotal;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderTaxTotal;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineItemInput;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use PHPUnit\Framework\TestCase;

final class IncomingOrderInputTest extends TestCase
{
    public function test_it_exposes_constructor_values(): void
    {
        $issueDate = new DateTimeImmutable('2024-04-02T00:00:00+02:00');
        $buyer = new KnownPartyInput('Buyer s.r.o.');
        $seller = new KnownPartyInput('Seller s.r.o.');
        $accounting = new KnownPartyInput('Accounting s.r.o.');
        $delivery = new KnownPartyInput('Delivery s.r.o.');

        $monetaryTotal = new IncomingOrderMonetaryTotal(
            payableAmount: 121.0,
            payableRoundingAmount: 0.0,
            taxExclusiveAmount: 100.0,
            taxInclusiveAmount: 121.0,
        );

        $taxExchangeRate = new IncomingOrderTaxExchangeRate(
            taxCurrency: Currency::CZK,
            referenceCurrencyRate: 1.0,
            taxCurrencyRate: 1.0,
            rateDate: $issueDate,
            exchangeMarketBic: null,
        );

        $taxTotal = new IncomingOrderTaxTotal(
            taxAmount: 21.0,
            taxSubTotals: [
                new IncomingOrderTaxSubTotal(
                    calculationMethod: IncomingOrderTaxCalculationMethod::Total,
                    scheme: IncomingOrderTaxScheme::Vat,
                    taxableAmount: 100.0,
                    taxAmount: 21.0,
                    taxPercentage: 21.0,
                    taxSchemeExtensionCode: null,
                ),
            ],
        );

        $input = new IncomingOrderInput(
            uuid: 'order-uuid-1',
            issueDate: $issueDate,
            currency: Currency::CZK,
            buyerCustomerParty: $buyer,
            sellerSupplierParty: $seller,
            monetaryTotal: $monetaryTotal,
            taxExchangeRate: $taxExchangeRate,
            taxTotal: $taxTotal,
            id: 'ESHOP-0001',
            accountingCustomerParty: $accounting,
            delivery: $delivery,
            note: 'Test note',
            partialDeliveryIndicator: true,
            paymentMeansCode: IncomingOrderPaymentMeansCode::BankAccount,
        );

        self::assertSame('order-uuid-1', $input->getUuid());
        self::assertSame($issueDate, $input->getIssueDate());
        self::assertSame(Currency::CZK, $input->getCurrency());
        self::assertSame($buyer, $input->getBuyerCustomerParty());
        self::assertSame($seller, $input->getSellerSupplierParty());
        self::assertSame($monetaryTotal, $input->getMonetaryTotal());
        self::assertSame($taxExchangeRate, $input->getTaxExchangeRate());
        self::assertSame($taxTotal, $input->getTaxTotal());
        self::assertSame('ESHOP-0001', $input->getId());
        self::assertSame($accounting, $input->getAccountingCustomerParty());
        self::assertSame($delivery, $input->getDelivery());
        self::assertSame('Test note', $input->getNote());
        self::assertTrue($input->isPartialDeliveryIndicator());
        self::assertSame(IncomingOrderPaymentMeansCode::BankAccount, $input->getPaymentMeansCode());
        self::assertSame([], $input->getDocumentLines());
    }

    public function test_with_methods_update_values(): void
    {
        $issueDate1 = new DateTimeImmutable('2024-04-02T00:00:00+02:00');
        $issueDate2 = new DateTimeImmutable('2024-05-03T00:00:00+02:00');

        $buyer1 = new KnownPartyInput('Buyer 1');
        $buyer2 = new KnownPartyInput('Buyer 2');

        $seller1 = new KnownPartyInput('Seller 1');
        $seller2 = new KnownPartyInput('Seller 2');

        $accounting = new KnownPartyInput('Accounting');
        $delivery = new KnownPartyInput('Delivery');

        $monetaryTotal1 = new IncomingOrderMonetaryTotal(
            payableAmount: 121.0,
            payableRoundingAmount: 0.0,
            taxExclusiveAmount: 100.0,
            taxInclusiveAmount: 121.0,
        );

        $monetaryTotal2 = new IncomingOrderMonetaryTotal(
            payableAmount: 242.0,
            payableRoundingAmount: 0.0,
            taxExclusiveAmount: 200.0,
            taxInclusiveAmount: 242.0,
        );

        $taxExchangeRate1 = new IncomingOrderTaxExchangeRate(
            taxCurrency: Currency::CZK,
            referenceCurrencyRate: 1.0,
            taxCurrencyRate: 1.0,
            rateDate: $issueDate1,
            exchangeMarketBic: null,
        );

        $taxExchangeRate2 = new IncomingOrderTaxExchangeRate(
            taxCurrency: Currency::EUR,
            referenceCurrencyRate: 1.0,
            taxCurrencyRate: 25.0,
            rateDate: $issueDate2,
            exchangeMarketBic: 'CNBACZPP',
        );

        $taxTotal1 = new IncomingOrderTaxTotal(
            taxAmount: 21.0,
            taxSubTotals: [
                new IncomingOrderTaxSubTotal(
                    calculationMethod: IncomingOrderTaxCalculationMethod::Total,
                    scheme: IncomingOrderTaxScheme::Vat,
                    taxableAmount: 100.0,
                    taxAmount: 21.0,
                    taxPercentage: 21.0,
                    taxSchemeExtensionCode: null,
                ),
            ],
        );

        $taxTotal2 = new IncomingOrderTaxTotal(
            taxAmount: 42.0,
            taxSubTotals: [
                new IncomingOrderTaxSubTotal(
                    calculationMethod: IncomingOrderTaxCalculationMethod::Total,
                    scheme: IncomingOrderTaxScheme::Vat,
                    taxableAmount: 200.0,
                    taxAmount: 42.0,
                    taxPercentage: 21.0,
                    taxSchemeExtensionCode: null,
                ),
            ],
        );

        $input = new IncomingOrderInput(
            uuid: 'order-uuid-1',
            issueDate: $issueDate1,
            currency: Currency::CZK,
            buyerCustomerParty: $buyer1,
            sellerSupplierParty: $seller1,
            monetaryTotal: $monetaryTotal1,
            taxExchangeRate: $taxExchangeRate1,
            taxTotal: $taxTotal1,
        );

        $result = $input
            ->withUuid('order-uuid-2')
            ->withIssueDate($issueDate2)
            ->withCurrency(Currency::EUR)
            ->withBuyerCustomerParty($buyer2)
            ->withSellerSupplierParty($seller2)
            ->withMonetaryTotal($monetaryTotal2)
            ->withTaxExchangeRate($taxExchangeRate2)
            ->withTaxTotal($taxTotal2)
            ->withId('ESHOP-0002')
            ->withAccountingCustomerParty($accounting)
            ->withDelivery($delivery)
            ->withNote('Updated note')
            ->withPartialDeliveryIndicator(true)
            ->withPaymentMeansCode(IncomingOrderPaymentMeansCode::Cheque);

        self::assertSame($input, $result);

        self::assertSame('order-uuid-2', $input->getUuid());
        self::assertSame($issueDate2, $input->getIssueDate());
        self::assertSame(Currency::EUR, $input->getCurrency());
        self::assertSame($buyer2, $input->getBuyerCustomerParty());
        self::assertSame($seller2, $input->getSellerSupplierParty());
        self::assertSame($monetaryTotal2, $input->getMonetaryTotal());
        self::assertSame($taxExchangeRate2, $input->getTaxExchangeRate());
        self::assertSame($taxTotal2, $input->getTaxTotal());
        self::assertSame('ESHOP-0002', $input->getId());
        self::assertSame($accounting, $input->getAccountingCustomerParty());
        self::assertSame($delivery, $input->getDelivery());
        self::assertSame('Updated note', $input->getNote());
        self::assertTrue($input->isPartialDeliveryIndicator());
        self::assertSame(IncomingOrderPaymentMeansCode::Cheque, $input->getPaymentMeansCode());
    }

    public function test_it_supports_nullable_optional_fields(): void
    {
        $input = new IncomingOrderInput(
            uuid: 'order-uuid-1',
            issueDate: new DateTimeImmutable('2024-04-02T00:00:00+02:00'),
            currency: Currency::CZK,
            buyerCustomerParty: new KnownPartyInput('Buyer'),
            sellerSupplierParty: new KnownPartyInput('Seller'),
            monetaryTotal: new IncomingOrderMonetaryTotal(
                payableAmount: 121.0,
                payableRoundingAmount: 0.0,
                taxExclusiveAmount: 100.0,
                taxInclusiveAmount: 121.0,
            ),
            taxExchangeRate: new IncomingOrderTaxExchangeRate(
                taxCurrency: Currency::CZK,
                referenceCurrencyRate: 1.0,
                taxCurrencyRate: 1.0,
                rateDate: null,
                exchangeMarketBic: null,
            ),
            taxTotal: new IncomingOrderTaxTotal(
                taxAmount: 21.0,
                taxSubTotals: [
                    new IncomingOrderTaxSubTotal(
                        calculationMethod: IncomingOrderTaxCalculationMethod::Total,
                        scheme: IncomingOrderTaxScheme::Vat,
                        taxableAmount: 100.0,
                        taxAmount: 21.0,
                        taxPercentage: 21.0,
                        taxSchemeExtensionCode: null,
                    ),
                ],
            ),
        );

        self::assertNull($input->getId());
        self::assertNull($input->getAccountingCustomerParty());
        self::assertNull($input->getDelivery());
        self::assertNull($input->getNote());
        self::assertFalse($input->isPartialDeliveryIndicator());
        self::assertNull($input->getPaymentMeansCode());

        $input
            ->withId(null)
            ->withAccountingCustomerParty(null)
            ->withDelivery(null)
            ->withNote(null)
            ->withPaymentMeansCode(null);

        self::assertNull($input->getId());
        self::assertNull($input->getAccountingCustomerParty());
        self::assertNull($input->getDelivery());
        self::assertNull($input->getNote());
        self::assertNull($input->getPaymentMeansCode());
    }

    public function test_it_can_add_and_replace_document_lines(): void
    {
        $input = new IncomingOrderInput(
            uuid: 'order-uuid-1',
            issueDate: new DateTimeImmutable('2024-04-02T00:00:00+02:00'),
            currency: Currency::CZK,
            buyerCustomerParty: new KnownPartyInput('Buyer'),
            sellerSupplierParty: new KnownPartyInput('Seller'),
            monetaryTotal: new IncomingOrderMonetaryTotal(
                payableAmount: 121.0,
                payableRoundingAmount: 0.0,
                taxExclusiveAmount: 100.0,
                taxInclusiveAmount: 121.0,
            ),
            taxExchangeRate: new IncomingOrderTaxExchangeRate(
                taxCurrency: Currency::CZK,
                referenceCurrencyRate: 1.0,
                taxCurrencyRate: 1.0,
                rateDate: null,
                exchangeMarketBic: null,
            ),
            taxTotal: new IncomingOrderTaxTotal(
                taxAmount: 21.0,
                taxSubTotals: [
                    new IncomingOrderTaxSubTotal(
                        calculationMethod: IncomingOrderTaxCalculationMethod::Total,
                        scheme: IncomingOrderTaxScheme::Vat,
                        taxableAmount: 100.0,
                        taxAmount: 21.0,
                        taxPercentage: 21.0,
                        taxSchemeExtensionCode: null,
                    ),
                ],
            ),
        );

        $line1 = $this->createLine('line-1', 100.0, 121.0);
        $line2 = $this->createLine('line-2', 200.0, 242.0);

        self::assertSame($input, $input->addDocumentLine($line1));
        self::assertSame([$line1], $input->getDocumentLines());

        self::assertSame($input, $input->withDocumentLines([$line1, $line2]));
        self::assertSame([$line1, $line2], $input->getDocumentLines());
    }

    private function createLine(string $uuid, float $base, float $incl): IncomingOrderLineInput
    {
        return new IncomingOrderLineInput(
            uuid: $uuid,
            lineExtensionAmount: $base,
            lineExtensionAmountTaxInclusive: $incl,
            lineItem: new IncomingOrderLineItemInput(
                catalogueItemIdentification: 'SKU-' . $uuid,
            ),
            lineQuantity: new IncomingOrderQuantity(
                value: 1.0,
                unitCode: 'Ks',
                scheme: IncomingOrderUnitOfMeasureScheme::Unknown,
            ),
            taxSubTotal: new IncomingOrderTaxSubTotal(
                calculationMethod: IncomingOrderTaxCalculationMethod::Add,
                scheme: IncomingOrderTaxScheme::Vat,
                taxableAmount: $base,
                taxAmount: $incl - $base,
                taxPercentage: 21.0,
                taxSchemeExtensionCode: null,
            ),
        );
    }
}
