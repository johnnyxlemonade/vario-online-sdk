<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\OutgoingQuotation\Builder;

use DateTimeImmutable;
use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Builder\OutgoingQuotationBuildInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Builder\OutgoingQuotationBuilder;
use Lemonade\Vario\Domain\OutgoingQuotation\Builder\OutgoingQuotationBuildPartiesInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Enum\OutgoingQuotationPaymentMeansCode;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationLineItemInput;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentPriceMode;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentDescription;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentBuildIdentityInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineIdentityInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLinePriceInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineQuantityInput;
use Lemonade\Vario\Normalizer\OutgoingQuotation\OutgoingQuotationInputNormalizer;
use Lemonade\Vario\Domain\Shared\Identification;
use Lemonade\Vario\Domain\Shared\IdentificationScheme;
use PHPUnit\Framework\TestCase;

final class OutgoingQuotationBuilderTest extends TestCase
{
    public function test_build_creates_expected_preview_payload(): void
    {
        $builder = new OutgoingQuotationBuilder();
        $normalizer = new OutgoingQuotationInputNormalizer();

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

        $quotation = $builder->build(new OutgoingQuotationBuildInput(
            identity: new DocumentBuildIdentityInput(
                uuid: 'c676048c-3789-4228-82b2-9ca6e7b952f7',
                id: 'ZAKTEST-2026-00002',
            ),
            issueDate: new DateTimeImmutable('2026-06-18T00:00:00+02:00'),
            currency: Currency::CZK,
            parties: new OutgoingQuotationBuildPartiesInput(
                buyerCustomerParty: $buyer,
                sellerSupplierParty: $seller,
            ),
            lines: [
                new DocumentCalculatedLineInput(
                    identity: new DocumentCalculatedLineIdentityInput(
                        uuid: 'd4b5b29c-d658-4568-aaa9-839f11ce1446',
                        id: '1',
                    ),
                    lineItem: (new OutgoingQuotationLineItemInput())
                        ->withCatalogueItemIdentification('A25882')
                        ->addDescription(new DocumentDescription('Adam kĹ™eslo - sklĂˇdaÄŤka')),
                    quantity: new DocumentCalculatedLineQuantityInput(
                        value: 1.0,
                        unitCode: 'Ks',
                    ),
                    price: new DocumentCalculatedLinePriceInput(
                        unitPrice: 95.0,
                        vatRate: 21.0,
                        priceMode: DocumentPriceMode::WithoutVat,
                    ),
                ),
            ],
            paymentMeansCode: OutgoingQuotationPaymentMeansCode::Cash,
            payableRoundingAmount: 0.05,
        ));

        self::assertSame([
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
                                'Text' => 'Adam kĹ™eslo - sklĂˇdaÄŤka',
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
        ], $normalizer->normalize($quotation));
    }

    public function test_build_transfers_line_allowance_and_immutable_with_method_preserves_it(): void
    {
        $quotation = (new OutgoingQuotationBuilder())->build(new OutgoingQuotationBuildInput(
            identity: new DocumentBuildIdentityInput(
                uuid: 'quotation-uuid',
            ),
            issueDate: new DateTimeImmutable('2026-06-18T00:00:00+02:00'),
            currency: Currency::CZK,
            parties: new OutgoingQuotationBuildPartiesInput(
                buyerCustomerParty: new KnownPartyInput('Buyer'),
                sellerSupplierParty: new KnownPartyInput('Seller'),
            ),
            lines: [
                new DocumentCalculatedLineInput(
                    identity: new DocumentCalculatedLineIdentityInput(
                        uuid: 'line-uuid',
                    ),
                    lineItem: new OutgoingQuotationLineItemInput(),
                    quantity: new DocumentCalculatedLineQuantityInput(
                        value: 1.0,
                        unitCode: 'Ks',
                    ),
                    price: new DocumentCalculatedLinePriceInput(
                        unitPrice: 9600.0,
                        vatRate: 21.0,
                        lineAllowanceAmount: 2400.0,
                    ),
                ),
            ],
        ));

        $line = $quotation->getDocumentLines()[0];
        self::assertSame(2400.0, $line->getLineAllowanceAmount());

        $changedLine = $line->withNote('Changed note');
        self::assertSame(2400.0, $changedLine->getLineAllowanceAmount());

        $payload = (new OutgoingQuotationInputNormalizer())->normalize($quotation);
        /** @var list<array<string, mixed>> $documentLines */
        $documentLines = $payload['DocumentLine'];
        self::assertIsArray($documentLines);
        /** @var array<string, mixed> $firstLine */
        $firstLine = $documentLines[0];
        self::assertIsArray($firstLine);
        self::assertSame(2400.0, $firstLine['LineAllowanceAmount']);
    }

    public function test_normalizer_omits_line_allowance_amount_when_null(): void
    {
        $quotation = (new OutgoingQuotationBuilder())->build(new OutgoingQuotationBuildInput(
            identity: new DocumentBuildIdentityInput(
                uuid: 'quotation-uuid',
            ),
            issueDate: new DateTimeImmutable('2026-06-18T00:00:00+02:00'),
            currency: Currency::CZK,
            parties: new OutgoingQuotationBuildPartiesInput(
                buyerCustomerParty: new KnownPartyInput('Buyer'),
                sellerSupplierParty: new KnownPartyInput('Seller'),
            ),
            lines: [
                new DocumentCalculatedLineInput(
                    identity: new DocumentCalculatedLineIdentityInput(
                        uuid: 'line-uuid',
                    ),
                    lineItem: new OutgoingQuotationLineItemInput(),
                    quantity: new DocumentCalculatedLineQuantityInput(
                        value: 1.0,
                        unitCode: 'Ks',
                    ),
                    price: new DocumentCalculatedLinePriceInput(
                        unitPrice: 100.0,
                        vatRate: 21.0,
                    ),
                ),
            ],
        ));

        $payload = (new OutgoingQuotationInputNormalizer())->normalize($quotation);
        /** @var list<array<string, mixed>> $documentLines */
        $documentLines = $payload['DocumentLine'];
        self::assertIsArray($documentLines);
        /** @var array<string, mixed> $firstLine */
        $firstLine = $documentLines[0];
        self::assertIsArray($firstLine);
        self::assertArrayNotHasKey('LineAllowanceAmount', $firstLine);
    }
}
