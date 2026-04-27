<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Builder;

use DateTimeImmutable;
use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\IncomingOrder\Builder\IncomingOrderBuilder;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPaymentMeansCode;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPriceMode;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTaxCalculationMethod;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTaxScheme;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderUnitOfMeasureScheme;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderTaxExchangeRate;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderCalculatedLineInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineItemInput;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use PHPUnit\Framework\TestCase;

final class IncomingOrderBuilderTest extends TestCase
{
    public function test_build_creates_complete_order_and_default_tax_exchange_rate(): void
    {
        $builder = new IncomingOrderBuilder();

        $issueDate = new DateTimeImmutable('2024-04-02T00:00:00+02:00');
        $buyer = new KnownPartyInput('Buyer s.r.o.');
        $seller = new KnownPartyInput('Seller s.r.o.');
        $accounting = new KnownPartyInput('Accounting s.r.o.');
        $delivery = new KnownPartyInput('Delivery s.r.o.');

        $lines = [
            new IncomingOrderCalculatedLineInput(
                uuid: 'line-1',
                lineItem: new IncomingOrderLineItemInput(
                    catalogueItemIdentification: 'SKU-001',
                ),
                quantity: 2.0,
                unitCode: 'Ks',
                unitPrice: 100.0,
                vatRate: 21.0,
                priceMode: IncomingOrderPriceMode::WithoutVat,
                unitScheme: IncomingOrderUnitOfMeasureScheme::Unknown,
                id: 'ROW-1',
                note: 'First row',
                taxCalculationMethod: IncomingOrderTaxCalculationMethod::Add,
                taxScheme: IncomingOrderTaxScheme::Vat,
                taxSchemeExtensionCode: null,
            ),
            new IncomingOrderCalculatedLineInput(
                uuid: 'line-2',
                lineItem: new IncomingOrderLineItemInput(
                    catalogueItemIdentification: 'SKU-002',
                ),
                quantity: 1.0,
                unitCode: 'Ks',
                unitPrice: 121.0,
                vatRate: 21.0,
                priceMode: IncomingOrderPriceMode::WithVat,
                unitScheme: IncomingOrderUnitOfMeasureScheme::Unknown,
                id: 'ROW-2',
                note: 'Second row',
                taxCalculationMethod: IncomingOrderTaxCalculationMethod::Add,
                taxScheme: IncomingOrderTaxScheme::Vat,
                taxSchemeExtensionCode: 'LOCAL-RC',
            ),
        ];

        $order = $builder->build(
            uuid: 'order-uuid-1',
            issueDate: $issueDate,
            currency: Currency::CZK,
            buyerCustomerParty: $buyer,
            sellerSupplierParty: $seller,
            lines: $lines,
            id: 'ORD-001',
            accountingCustomerParty: $accounting,
            delivery: $delivery,
            note: 'Header note',
            partialDeliveryIndicator: true,
            paymentMeansCode: IncomingOrderPaymentMeansCode::BankAccount,
        );

        self::assertSame('order-uuid-1', $order->getUuid());
        self::assertSame($issueDate, $order->getIssueDate());
        self::assertSame(Currency::CZK, $order->getCurrency());
        self::assertSame($buyer, $order->getBuyerCustomerParty());
        self::assertSame($seller, $order->getSellerSupplierParty());
        self::assertSame($accounting, $order->getAccountingCustomerParty());
        self::assertSame($delivery, $order->getDelivery());
        self::assertSame('ORD-001', $order->getId());
        self::assertSame('Header note', $order->getNote());
        self::assertTrue($order->isPartialDeliveryIndicator());
        self::assertSame(IncomingOrderPaymentMeansCode::BankAccount, $order->getPaymentMeansCode());

        self::assertCount(2, $order->getDocumentLines());

        $line1 = $order->getDocumentLines()[0];
        self::assertSame('line-1', $line1->getUuid());
        self::assertSame('ROW-1', $line1->getId());
        self::assertSame('First row', $line1->getNote());
        self::assertSame(200.0, $line1->getLineExtensionAmount());
        self::assertSame(242.0, $line1->getLineExtensionAmountTaxInclusive());
        self::assertSame(2.0, $line1->getLineQuantity()->getValue());
        self::assertSame(42.0, $line1->getTaxSubTotal()->getTaxAmount());

        $line2 = $order->getDocumentLines()[1];
        self::assertSame('line-2', $line2->getUuid());
        self::assertSame('ROW-2', $line2->getId());
        self::assertSame('Second row', $line2->getNote());
        self::assertSame(100.0, $line2->getLineExtensionAmount());
        self::assertSame(121.0, $line2->getLineExtensionAmountTaxInclusive());
        self::assertSame(1.0, $line2->getLineQuantity()->getValue());
        self::assertSame(21.0, $line2->getTaxSubTotal()->getTaxAmount());
        self::assertSame('LOCAL-RC', $line2->getTaxSubTotal()->getTaxSchemeExtensionCode());

        $monetaryTotal = $order->getMonetaryTotal();
        self::assertSame(363.0, $monetaryTotal->getPayableAmount());
        self::assertSame(0.0, $monetaryTotal->getPayableRoundingAmount());
        self::assertSame(300.0, $monetaryTotal->getTaxExclusiveAmount());
        self::assertSame(363.0, $monetaryTotal->getTaxInclusiveAmount());

        $taxTotal = $order->getTaxTotal();
        self::assertSame(63.0, $taxTotal->getTaxAmount());
        self::assertCount(2, $taxTotal->getTaxSubTotals());

        $taxExchangeRate = $order->getTaxExchangeRate();
        self::assertSame(Currency::CZK, $taxExchangeRate->getTaxCurrency());
        self::assertSame(1.0, $taxExchangeRate->getReferenceCurrencyRate());
        self::assertSame(1.0, $taxExchangeRate->getTaxCurrencyRate());
        self::assertSame($issueDate, $taxExchangeRate->getRateDate());
        self::assertNull($taxExchangeRate->getExchangeMarketBic());
    }

    public function test_build_uses_explicit_tax_exchange_rate_when_provided(): void
    {
        $builder = new IncomingOrderBuilder();

        $issueDate = new DateTimeImmutable('2024-04-02T00:00:00+02:00');
        $buyer = new KnownPartyInput('Buyer s.r.o.');
        $seller = new KnownPartyInput('Seller s.r.o.');

        $customTaxExchangeRate = new IncomingOrderTaxExchangeRate(
            taxCurrency: Currency::EUR,
            referenceCurrencyRate: 1.0,
            taxCurrencyRate: 25.0,
            rateDate: new DateTimeImmutable('2024-04-01T00:00:00+02:00'),
            exchangeMarketBic: 'CNBACZPP',
        );

        $order = $builder->build(
            uuid: 'order-uuid-2',
            issueDate: $issueDate,
            currency: Currency::CZK,
            buyerCustomerParty: $buyer,
            sellerSupplierParty: $seller,
            lines: [
                new IncomingOrderCalculatedLineInput(
                    uuid: 'line-1',
                    lineItem: new IncomingOrderLineItemInput(
                        catalogueItemIdentification: 'SKU-001',
                    ),
                    quantity: 1.0,
                    unitCode: 'Ks',
                    unitPrice: 100.0,
                    vatRate: 21.0,
                ),
            ],
            taxExchangeRate: $customTaxExchangeRate,
        );

        self::assertSame($customTaxExchangeRate, $order->getTaxExchangeRate());
    }
}
