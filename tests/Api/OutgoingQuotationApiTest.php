<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Api;

use DateTimeImmutable;
use Lemonade\Vario\Api\OutgoingQuotationApi;
use Lemonade\Vario\Client\VarioClientInterface;
use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Enum\OutgoingQuotationPaymentMeansCode;
use Lemonade\Vario\Domain\OutgoingQuotation\Enum\OutgoingQuotationTaxCalculationMethod;
use Lemonade\Vario\Domain\OutgoingQuotation\Enum\OutgoingQuotationTaxScheme;
use Lemonade\Vario\Domain\OutgoingQuotation\Enum\OutgoingQuotationUnitOfMeasureScheme;
use Lemonade\Vario\Domain\OutgoingQuotation\Result\OutgoingQuotationUpsertResult;
use Lemonade\Vario\Domain\OutgoingQuotation\ValueObject\OutgoingQuotationDescription;
use Lemonade\Vario\Domain\OutgoingQuotation\ValueObject\OutgoingQuotationMonetaryTotal;
use Lemonade\Vario\Domain\OutgoingQuotation\ValueObject\OutgoingQuotationQuantity;
use Lemonade\Vario\Domain\OutgoingQuotation\ValueObject\OutgoingQuotationTaxExchangeRate;
use Lemonade\Vario\Domain\OutgoingQuotation\ValueObject\OutgoingQuotationTaxSubTotal;
use Lemonade\Vario\Domain\OutgoingQuotation\ValueObject\OutgoingQuotationTaxTotal;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationLineInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationLineItemInput;
use Lemonade\Vario\Domain\Shared\Identification;
use Lemonade\Vario\Domain\Shared\IdentificationScheme;
use Lemonade\Vario\Enum\HttpMethod;
use Lemonade\Vario\Enum\VarioEndpoint;
use Lemonade\Vario\Normalizer\OutgoingQuotation\OutgoingQuotationInputNormalizer;
use Lemonade\Vario\ValueObject\OutgoingQuotationQuery;
use PHPUnit\Framework\TestCase;

final class OutgoingQuotationApiTest extends TestCase
{
    public function test_query_calls_client_with_query_payload(): void
    {
        $client = $this->createMock(VarioClientInterface::class);
        $query = (new OutgoingQuotationQuery())
            ->withPageIndex(2)
            ->withPageLength(25)
            ->withSort('IssueDate');

        $expected = [
            [
                'UUID' => 'c676048c-3789-4228-82b2-9ca6e7b952f7',
            ],
        ];

        $client
            ->expects(self::once())
            ->method('sendJson')
            ->with(
                HttpMethod::QUERY,
                VarioEndpoint::OutgoingQuotation->value,
                $query->toArray(),
            )
            ->willReturn($expected);

        $api = new OutgoingQuotationApi(
            $client,
            new OutgoingQuotationInputNormalizer(),
        );

        self::assertSame($expected, $api->query($query));
    }

    public function test_preview_upsert_produces_expected_payload(): void
    {
        $api = new OutgoingQuotationApi(
            $this->createMock(VarioClientInterface::class),
            new OutgoingQuotationInputNormalizer(),
        );

        $payload = $api->previewUpsert([
            $this->createOutgoingQuotationInput(),
        ]);

        self::assertSame([
            [
                'BuyerCustomerParty' => [
                    'Name' => 'A - Storex, v.o.s.',
                    'Identifications' => [
                        [
                            'ID' => '620927153',
                            'Scheme' => 'UIN',
                            'OriginCountry' => 'CZ',
                        ],
                    ],
                ],
                'SellerSupplierParty' => [
                    'Identifications' => [
                        [
                            'ID' => 'CZ61681229',
                            'Scheme' => 'VAT',
                            'OriginCountry' => 'CZ',
                        ],
                    ],
                ],
                'ID' => 'ZAKTEST-2026-00002',
                'UUID' => 'c676048c-3789-4228-82b2-9ca6e7b952f7',
                'IssueDate' => '2026-06-18T00:00:00+02:00',
                'Currency' => 'CZK',
                'PaymentMeansCode' => 'Cash',
                'DocumentLine' => [
                    [
                        'ID' => '1',
                        'UUID' => 'd4b5b29c-d658-4568-aaa9-839f11ce1446',
                        'LineExtensionAmount' => 95.0,
                        'LineExtensionAmountTaxInclusive' => 114.95,
                        'LineItem' => [
                            'CatalogueItemIdentification' => 'A25882',
                            'Description' => [
                                [
                                    'Text' => 'Adam křeslo - skládačka',
                                ],
                            ],
                        ],
                        'LineQuantity' => [
                            'Scheme' => 'Unknown',
                            'UnitCode' => 'Ks',
                            'Value' => 1.0,
                        ],
                        'TaxSubTotal' => [
                            'CalculationMethod' => 'Add',
                            'Scheme' => 'Vat',
                            'TaxableAmount' => 95.0,
                            'TaxAmount' => 19.95,
                            'TaxPercentage' => 21.0,
                        ],
                    ],
                ],
                'MonetaryTotal' => [
                    'TaxExclusiveAmount' => 95.0,
                    'TaxInclusiveAmount' => 114.95,
                    'PayableRoundingAmount' => 0.05,
                    'PayableAmount' => 115.0,
                ],
                'TaxExchangeRate' => [
                    'ReferenceCurrencyRate' => 1.0,
                    'TaxCurrency' => 'CZK',
                    'TaxCurrencyRate' => 1.0,
                ],
                'TaxTotal' => [
                    'TaxAmount' => 19.95,
                    'TaxSubTotal' => [
                        [
                            'CalculationMethod' => 'Total',
                            'Scheme' => 'Vat',
                            'TaxableAmount' => 95.0,
                            'TaxAmount' => 19.95,
                            'TaxPercentage' => 21.0,
                        ],
                    ],
                ],
            ],
        ], $payload);
    }

    public function test_upsert_calls_client_with_normalized_payload_and_maps_result(): void
    {
        $client = $this->createMock(VarioClientInterface::class);

        $api = new OutgoingQuotationApi(
            $client,
            new OutgoingQuotationInputNormalizer(),
        );

        $quotation = $this->createOutgoingQuotationInput();
        $expectedPayload = $api->previewUpsert([$quotation]);

        $response = [
            [
                'IssuerObjectID' => '1001',
                'RecipientObjectID' => 'ESHOP-1001',
                'IssueDate' => '2026-06-18T00:00:00+02:00',
                'ReceiveDate' => '2026-06-19T00:00:00+02:00',
                'UUID' => 'c676048c-3789-4228-82b2-9ca6e7b952f7',
            ],
        ];

        $client
            ->expects(self::once())
            ->method('sendJson')
            ->with(
                HttpMethod::PUT,
                VarioEndpoint::OutgoingQuotation->value,
                $expectedPayload,
            )
            ->willReturn($response);

        $result = $api->upsert([$quotation]);

        self::assertCount(1, $result);
        self::assertInstanceOf(OutgoingQuotationUpsertResult::class, $result[0]);
        self::assertSame([
            'uuid' => 'c676048c-3789-4228-82b2-9ca6e7b952f7',
            'issuerObjectId' => '1001',
            'recipientObjectId' => 'ESHOP-1001',
            'issueDate' => '2026-06-18T00:00:00+02:00',
            'receiveDate' => '2026-06-19T00:00:00+02:00',
        ], $result[0]->toArray());
    }

    private function createOutgoingQuotationInput(): OutgoingQuotationInput
    {
        $buyer = (new KnownPartyInput('A - Storex, v.o.s.'))
            ->addIdentification(new Identification(
                scheme: IdentificationScheme::UIN,
                id: '620927153',
                originCountry: 'CZ',
            ));

        $seller = (new KnownPartyInput(''))
            ->addIdentification(new Identification(
                scheme: IdentificationScheme::VAT,
                id: 'CZ61681229',
                originCountry: 'CZ',
            ));

        $lineItem = (new OutgoingQuotationLineItemInput())
            ->withCatalogueItemIdentification('A25882')
            ->addDescription(new OutgoingQuotationDescription('Adam křeslo - skládačka'));

        return new OutgoingQuotationInput(
            uuid: 'c676048c-3789-4228-82b2-9ca6e7b952f7',
            issueDate: new DateTimeImmutable('2026-06-18T00:00:00+02:00'),
            currency: Currency::CZK,
            buyerCustomerParty: $buyer,
            sellerSupplierParty: $seller,
            monetaryTotal: new OutgoingQuotationMonetaryTotal(
                payableAmount: 115.0,
                payableRoundingAmount: 0.05,
                taxExclusiveAmount: 95.0,
                taxInclusiveAmount: 114.95,
            ),
            taxExchangeRate: new OutgoingQuotationTaxExchangeRate(
                taxCurrency: Currency::CZK,
                referenceCurrencyRate: 1.0,
                taxCurrencyRate: 1.0,
            ),
            taxTotal: new OutgoingQuotationTaxTotal(
                taxAmount: 19.95,
                taxSubTotals: [
                    new OutgoingQuotationTaxSubTotal(
                        calculationMethod: OutgoingQuotationTaxCalculationMethod::Total,
                        scheme: OutgoingQuotationTaxScheme::Vat,
                        taxableAmount: 95.0,
                        taxAmount: 19.95,
                        taxPercentage: 21.0,
                    ),
                ],
            ),
            documentLines: [
                new OutgoingQuotationLineInput(
                    uuid: 'd4b5b29c-d658-4568-aaa9-839f11ce1446',
                    lineExtensionAmount: 95.0,
                    lineExtensionAmountTaxInclusive: 114.95,
                    lineItem: $lineItem,
                    lineQuantity: new OutgoingQuotationQuantity(
                        value: 1.0,
                        unitCode: 'Ks',
                        scheme: OutgoingQuotationUnitOfMeasureScheme::Unknown,
                    ),
                    taxSubTotal: new OutgoingQuotationTaxSubTotal(
                        calculationMethod: OutgoingQuotationTaxCalculationMethod::Add,
                        scheme: OutgoingQuotationTaxScheme::Vat,
                        taxableAmount: 95.0,
                        taxAmount: 19.95,
                        taxPercentage: 21.0,
                    ),
                    id: '1',
                ),
            ],
            id: 'ZAKTEST-2026-00002',
            paymentMeansCode: OutgoingQuotationPaymentMeansCode::Cash,
        );
    }
}
