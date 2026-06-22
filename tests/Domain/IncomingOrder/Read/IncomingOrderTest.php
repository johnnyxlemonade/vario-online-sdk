<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Read;

use DateTimeImmutable;
use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPaymentMeansCode;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxCalculationMethod;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxScheme;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTextualAttributeKind;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrder;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderDeliveryDetail;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentDescription;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderLine;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderLineItem;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderParty;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderTextualAttribute;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentMonetaryTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxExchangeRate;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxSubTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxTotal;
use PHPUnit\Framework\TestCase;

final class IncomingOrderTest extends TestCase
{
    public function test_it_exposes_all_values(): void
    {
        $issueDate = new DateTimeImmutable('2025-05-06T00:00:00+01:00');

        $buyer = new IncomingOrderParty(name: 'Buyer s.r.o.');
        $accounting = new IncomingOrderParty(name: 'Accounting s.r.o.');
        $delivery = new IncomingOrderParty(name: 'Delivery s.r.o.');
        $seller = new IncomingOrderParty(name: 'Seller s.r.o.');

        $line = new IncomingOrderLine(
            uuid: 'line-uuid-1',
            lineExtensionAmount: 100.0,
            lineExtensionAmountTaxInclusive: 121.0,
            lineItem: new IncomingOrderLineItem(
                catalogueItemIdentification: 'SKU-001',
                descriptions: [
                    new DocumentDescription('Test item', 'cs'),
                ],
            ),
        );

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
            exchangeMarketBic: 'CNBACZPP',
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
                    taxSchemeExtensionCode: null,
                ),
            ],
        );

        $deliveryDetail = new IncomingOrderDeliveryDetail(
            optionCodes: ['raben 7.5.2025'],
            requestedDeliveryDate: $issueDate,
        );

        $textualAttribute = new IncomingOrderTextualAttribute(
            attributeKind: DocumentTextualAttributeKind::ExtendedID,
            name: 'Cislo_dokladu',
            value: 'D-25-02102',
            langId: 'cs',
        );

        $order = new IncomingOrder(
            uuid: 'order-uuid-1',
            id: 'ESHOP-0001',
            issueDate: $issueDate,
            currency: Currency::CZK,
            note: 'Test note',
            partialDeliveryIndicator: true,
            paymentMeansCode: IncomingOrderPaymentMeansCode::BankAccount,
            buyerCustomerParty: $buyer,
            accountingCustomerParty: $accounting,
            delivery: $delivery,
            sellerSupplierParty: $seller,
            documentLines: [$line],
            monetaryTotal: $monetaryTotal,
            taxExchangeRate: $taxExchangeRate,
            taxTotal: $taxTotal,
            deliveryDetail: $deliveryDetail,
            textualAttributes: [$textualAttribute],
            extra: [
                'CustomField' => 'custom-value',
            ],
        );

        self::assertSame('order-uuid-1', $order->getUuid());
        self::assertSame('ESHOP-0001', $order->getId());
        self::assertSame($issueDate, $order->getIssueDate());
        self::assertSame(Currency::CZK, $order->getCurrency());
        self::assertSame('Test note', $order->getNote());
        self::assertTrue($order->getPartialDeliveryIndicator());
        self::assertSame(IncomingOrderPaymentMeansCode::BankAccount, $order->getPaymentMeansCode());

        self::assertSame($buyer, $order->getBuyerCustomerParty());
        self::assertSame($accounting, $order->getAccountingCustomerParty());
        self::assertSame($delivery, $order->getDelivery());
        self::assertSame($seller, $order->getSellerSupplierParty());

        self::assertSame([$line], $order->getDocumentLines());
        self::assertTrue($order->hasDocumentLines());

        self::assertSame($monetaryTotal, $order->getMonetaryTotal());
        self::assertTrue($order->hasMonetaryTotal());

        self::assertSame($taxExchangeRate, $order->getTaxExchangeRate());

        self::assertSame($taxTotal, $order->getTaxTotal());
        self::assertTrue($order->hasTaxTotal());

        self::assertSame($deliveryDetail, $order->getDeliveryDetail());
        self::assertTrue($order->hasDeliveryDetail());

        self::assertSame([$textualAttribute], $order->getTextualAttributes());
        self::assertTrue($order->hasTextualAttributes());

        self::assertSame([
            'CustomField' => 'custom-value',
        ], $order->getExtra());
    }

    public function test_it_supports_empty_optional_state(): void
    {
        $order = new IncomingOrder(
            uuid: 'order-uuid-1',
        );

        self::assertSame('order-uuid-1', $order->getUuid());
        self::assertNull($order->getId());
        self::assertNull($order->getIssueDate());
        self::assertNull($order->getCurrency());
        self::assertNull($order->getNote());
        self::assertNull($order->getPartialDeliveryIndicator());
        self::assertNull($order->getPaymentMeansCode());

        self::assertNull($order->getBuyerCustomerParty());
        self::assertNull($order->getAccountingCustomerParty());
        self::assertNull($order->getDelivery());
        self::assertNull($order->getSellerSupplierParty());

        self::assertSame([], $order->getDocumentLines());
        self::assertFalse($order->hasDocumentLines());

        self::assertNull($order->getMonetaryTotal());
        self::assertFalse($order->hasMonetaryTotal());

        self::assertNull($order->getTaxExchangeRate());

        self::assertNull($order->getTaxTotal());
        self::assertFalse($order->hasTaxTotal());

        self::assertNull($order->getDeliveryDetail());
        self::assertFalse($order->hasDeliveryDetail());

        self::assertSame([], $order->getTextualAttributes());
        self::assertFalse($order->hasTextualAttributes());

        self::assertSame([], $order->getExtra());
    }
}
