<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Api;

use DateTimeImmutable;
use Lemonade\Vario\Api\IncomingOrderApi;
use Lemonade\Vario\Client\VarioClientInterface;
use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPaymentMeansCode;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxCalculationMethod;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxScheme;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrder;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentDescription;
use Lemonade\Vario\Domain\IncomingOrder\Result\IncomingOrderUpsertResult;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineItemInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderPartiesInput;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentMonetaryTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentQuantity;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxExchangeRate;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxSubTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxTotal;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentIdentityInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentTotalsInput;
use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Enum\VarioEndpoint;
use Lemonade\Vario\Mapper\IncomingOrder\IncomingOrderMapper;
use Lemonade\Vario\Normalizer\IncomingOrder\IncomingOrderInputNormalizer;
use Lemonade\Vario\ValueObject\IncomingOrderQuery;
use PHPUnit\Framework\TestCase;

final class IncomingOrderApiTest extends TestCase
{
    public function test_query_calls_client_and_maps_result(): void
    {
        $client = $this->createMock(VarioClientInterface::class);
        $query = new IncomingOrderQuery();

        $client
            ->expects(self::once())
            ->method('sendJson')
            ->with(
                HttpMethod::QUERY,
                VarioEndpoint::IncomingOrder->value,
                $query->toArray()
            )
            ->willReturn([
                [
                    'UUID' => 'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
                    'IssueDate' => '2024-04-02T00:00:00+02:00',
                    'Currency' => 'CZK',
                    'BuyerCustomerParty' => [
                        'Name' => 'Buyer s.r.o.',
                    ],
                    'SellerSupplierParty' => [
                        'Name' => 'Seller s.r.o.',
                    ],
                    'DocumentLine' => [],
                    'MonetaryTotal' => [
                        'PayableAmount' => 121.0,
                        'PayableRoundingAmount' => 0.0,
                        'TaxExclusiveAmount' => 100.0,
                        'TaxInclusiveAmount' => 121.0,
                    ],
                    'TaxExchangeRate' => [
                        'TaxCurrency' => 'CZK',
                        'ReferenceCurrencyRate' => 1.0,
                        'TaxCurrencyRate' => 1.0,
                    ],
                    'TaxTotal' => [
                        'TaxAmount' => 21.0,
                        'TaxSubTotal' => [],
                    ],
                ],
            ]);

        $api = new IncomingOrderApi(
            $client,
            new IncomingOrderMapper(),
            new IncomingOrderInputNormalizer(),
        );

        $result = $api->query($query);

        self::assertCount(1, $result);
        self::assertInstanceOf(IncomingOrder::class, $result[0]);
        self::assertSame(
            'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
            $result[0]->getUuid()
        );
        self::assertSame('CZK', $result[0]->getCurrency()?->value);
    }

    public function test_upsert_calls_client_with_normalized_payload(): void
    {
        $client = $this->createMock(VarioClientInterface::class);

        $api = new IncomingOrderApi(
            $client,
            new IncomingOrderMapper(),
            new IncomingOrderInputNormalizer(),
        );

        $order = $this->createIncomingOrderInput();
        $expectedPayload = $api->previewUpsert([$order]);

        $response = [
            [
                'IssuerObjectID' => '1001',
                'RecipientObjectID' => 'ESHOP-1001',
                'IssueDate' => '2024-04-02T00:00:00+02:00',
                'ReceiveDate' => '2024-04-02T00:00:00+02:00',
                'UUID' => 'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
            ],
        ];

        $client
            ->expects(self::once())
            ->method('sendJson')
            ->with(
                HttpMethod::PUT,
                VarioEndpoint::IncomingOrder->value,
                $expectedPayload
            )
            ->willReturn($response);

        $result = $api->upsert([$order]);

        self::assertCount(1, $result);
        self::assertInstanceOf(IncomingOrderUpsertResult::class, $result[0]);

        self::assertSame([
            'uuid' => 'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
            'issuerObjectId' => '1001',
            'recipientObjectId' => 'ESHOP-1001',
            'issueDate' => '2024-04-02T00:00:00+02:00',
            'receiveDate' => '2024-04-02T00:00:00+02:00',
        ], $result[0]->toArray());
    }

    private function createIncomingOrderInput(): IncomingOrderInput
    {
        $buyer = new KnownPartyInput('Buyer s.r.o.');
        $seller = new KnownPartyInput('Seller s.r.o.');

        $lineItem = (new IncomingOrderLineItemInput())
            ->withCatalogueItemIdentification('SKU-001')
            ->withSellersItemIdentification('SKU-001')
            ->addDescription(
                new DocumentDescription('Test item')
            );

        $line = new IncomingOrderLineInput(
            uuid: 'd2045e34-49b4-4238-84e2-950362f2007e',
            lineExtensionAmount: 100.0,
            lineExtensionAmountTaxInclusive: 121.0,
            lineItem: $lineItem,
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
                taxSchemeExtensionCode: null,
            ),
        );

        return (new IncomingOrderInput(
            identity: new DocumentIdentityInput(
                uuid: 'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
            ),
            issueDate: new DateTimeImmutable('2024-04-02T00:00:00+02:00'),
            currency: Currency::CZK,
            parties: new IncomingOrderPartiesInput(
                buyerCustomerParty: $buyer,
                sellerSupplierParty: $seller,
            ),
            totals: new DocumentTotalsInput(
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
                    rateDate: new DateTimeImmutable('2024-04-02T00:00:00+02:00'),
                    exchangeMarketBic: null,
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
                            taxSchemeExtensionCode: null,
                        ),
                    ],
                ),
            ),
            paymentMeansCode: IncomingOrderPaymentMeansCode::BankAccount,
        ))->addDocumentLine($line);
    }
}
