<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Write;

use DateTimeImmutable;
use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPaymentMeansCode;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineItemInput;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxCalculationMethod;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxScheme;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTextualAttributeKind;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentDescription;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentMonetaryTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentQuantity;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxExchangeRate;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxSubTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentUnitConversionFactor;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentAdditionalAttributeInput;
use Lemonade\Vario\Domain\Shared\Identification;
use Lemonade\Vario\Domain\Shared\IdentificationScheme;
use Lemonade\Vario\Domain\Shared\PostalAddress;
use Lemonade\Vario\Normalizer\IncomingOrder\IncomingOrderInputNormalizer;
use PHPUnit\Framework\TestCase;

final class IncomingOrderInputNormalizerTest extends TestCase
{
    public function test_normalize_returns_expected_full_payload(): void
    {
        $normalizer = new IncomingOrderInputNormalizer();
        $issueDate = new DateTimeImmutable('2024-04-02T00:00:00+02:00');

        $buyer = $this->createParty(
            name: '1. česká podvodná',
            contactPerson: 'Rybana Wassermannová',
            email: 'pod.vodnik@zaby.cz',
            telephone: '+420557788996',
            address: new PostalAddress(
                street: 'Vodná',
                buildingNumber: '57',
                city: 'Žabovřesky',
                postalCode: '566 00',
                countryIso: 'CZ',
            ),
            identifications: [
                new Identification(
                    scheme: IdentificationScheme::UIN,
                    id: '89745612',
                    originCountry: 'CZ',
                ),
                new Identification(
                    scheme: IdentificationScheme::VAT,
                    id: 'CZ89745612',
                    originCountry: 'CZ',
                ),
            ],
        );

        $accounting = $this->createParty(
            name: '1. česká podvodná',
            contactPerson: 'Rybana Wassermannová',
            email: 'pod.vodnik@zaby.cz',
            telephone: '+420557788996',
            address: new PostalAddress(
                street: 'Vodná',
                buildingNumber: '57',
                city: 'Žabovřesky',
                postalCode: '566 00',
                countryIso: 'CZ',
            ),
            identifications: [
                new Identification(
                    scheme: IdentificationScheme::UIN,
                    id: '89745612',
                    originCountry: 'CZ',
                ),
                new Identification(
                    scheme: IdentificationScheme::VAT,
                    id: 'CZ89745612',
                    originCountry: 'CZ',
                ),
            ],
        );

        $delivery = $this->createParty(
            name: '1. česká podvodná',
            contactPerson: 'Vodomil Wassermann',
            email: 'pod.vodnik@zaby.cz',
            telephone: '+420557788996',
            address: new PostalAddress(
                street: 'Vodná',
                buildingNumber: '57',
                city: 'Žabovřesky',
                postalCode: '566 00',
                countryIso: 'CZ',
            ),
            identifications: [
                new Identification(
                    scheme: IdentificationScheme::UIN,
                    id: '89745612',
                    originCountry: 'CZ',
                ),
                new Identification(
                    scheme: IdentificationScheme::VAT,
                    id: 'CZ89745612',
                    originCountry: 'CZ',
                ),
            ],
        );

        $seller = $this->createParty(
            name: '',
            identifications: [
                new Identification(
                    scheme: IdentificationScheme::VAT,
                    id: 'CZ11223344',
                    originCountry: 'CZ',
                ),
            ],
        );

        $lineItem1 = (new IncomingOrderLineItemInput())
            ->withCatalogueItemIdentification('grosh big')
            ->withSellersItemIdentification('děravý groš')
            ->addDescription(new DocumentDescription('děravý groš'))
            ->addAdditionalAttribute(new DocumentAdditionalAttributeInput(
                name: 'Varianta',
                value: 'velká díra',
                attributeKind: DocumentTextualAttributeKind::ExtendedID,
                langId: null,
                unitCode: null,
                scheme: DocumentUnitOfMeasureScheme::Unknown,
            ))
            ->addUnitConversionFactor(new DocumentUnitConversionFactor(
                value: 1.0,
                unitCode: 'Ks',
                scheme: DocumentUnitOfMeasureScheme::Unknown,
            ));

        $lineItem2 = (new IncomingOrderLineItemInput())
            ->withCatalogueItemIdentification('BELA')
            ->withSellersItemIdentification('stará bela')
            ->addDescription(new DocumentDescription('stará bela'))
            ->addUnitConversionFactor(new DocumentUnitConversionFactor(
                value: 1.0,
                unitCode: 'Ks',
                scheme: DocumentUnitOfMeasureScheme::Unknown,
            ))
            ->addUnitConversionFactor(new DocumentUnitConversionFactor(
                value: 2.0,
                unitCode: 'm2',
                scheme: DocumentUnitOfMeasureScheme::SI,
            ));

        $line1 = new IncomingOrderLineInput(
            uuid: 'd2045e34-49b4-4238-84e2-950362f2007e',
            lineExtensionAmount: 1500.0,
            lineExtensionAmountTaxInclusive: 1815.0,
            lineItem: $lineItem1,
            lineQuantity: $this->createQuantity(1.0, 'Ks', DocumentUnitOfMeasureScheme::Unknown),
            taxSubTotal: $this->createTaxSubTotalAdd(1500.0, 315.0, 21.0),
        );

        $line2 = new IncomingOrderLineInput(
            uuid: '935f3ea7-3fda-40d8-af8f-a5582fc81f54',
            lineExtensionAmount: 300.0,
            lineExtensionAmountTaxInclusive: 363.0,
            lineItem: $lineItem2,
            lineQuantity: $this->createQuantity(1.0, 'Ks', DocumentUnitOfMeasureScheme::Unknown),
            taxSubTotal: $this->createTaxSubTotalAdd(300.0, 63.0, 21.0),
        );

        $input = new IncomingOrderInput(
            uuid: 'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
            issueDate: $issueDate,
            currency: Currency::CZK,
            buyerCustomerParty: $buyer,
            sellerSupplierParty: $seller,
            monetaryTotal: new DocumentMonetaryTotal(
                payableAmount: 2178.0,
                payableRoundingAmount: 0.0,
                taxExclusiveAmount: 1800.0,
                taxInclusiveAmount: 2178.0,
            ),
            taxExchangeRate: new DocumentTaxExchangeRate(
                taxCurrency: Currency::CZK,
                referenceCurrencyRate: 1.0,
                taxCurrencyRate: 1.0,
                rateDate: $issueDate,
                exchangeMarketBic: null,
            ),
            taxTotal: new DocumentTaxTotal(
                taxAmount: 378.0,
                taxSubTotals: [
                    $this->createTaxSubTotalTotal(1800.0, 378.0, 21.0),
                ],
            ),
            id: 'eshop0001',
            accountingCustomerParty: $accounting,
            delivery: $delivery,
            note: null,
            partialDeliveryIndicator: false,
            paymentMeansCode: IncomingOrderPaymentMeansCode::Cheque,
        );

        $input
            ->addDocumentLine($line1)
            ->addDocumentLine($line2);

        self::assertSame([
            'BuyerCustomerParty' => [
                'ContactPerson' => 'Rybana Wassermannová',
                'ElectronicMail' => 'pod.vodnik@zaby.cz',
                'Name' => '1. česká podvodná',
                'PostalAddress' => [
                    'StreetName' => 'Vodná',
                    'BuildingNumber' => '57',
                    'CityName' => 'Žabovřesky',
                    'PostalZone' => '566 00',
                    'CountryIso' => 'CZ',
                ],
                'Telephone' => '+420557788996',
                'Identifications' => [
                    [
                        'ID' => '89745612',
                        'Scheme' => 'UIN',
                        'OriginCountry' => 'CZ',
                    ],
                    [
                        'ID' => 'CZ89745612',
                        'Scheme' => 'VAT',
                        'OriginCountry' => 'CZ',
                    ],
                ],
            ],
            'Currency' => 'CZK',
            'DocumentLine' => [
                [
                    'LineExtensionAmount' => 1500.0,
                    'LineExtensionAmountTaxInclusive' => 1815.0,
                    'LineItem' => [
                        'CatalogueItemIdentification' => 'grosh big',
                        'SellersItemIdentification' => 'děravý groš',
                        'Description' => [
                            [
                                'Text' => 'děravý groš',
                            ],
                        ],
                        'AdditionalAttribute' => [
                            [
                                'AttributeKind' => 'ExtendedID',
                                'Name' => 'Varianta',
                                'Value' => 'velká díra',
                                'Scheme' => 'Unknown',
                            ],
                        ],
                        'UnitConversionFactor' => [
                            [
                                'Scheme' => 'Unknown',
                                'UnitCode' => 'Ks',
                                'Value' => 1.0,
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
                        'TaxableAmount' => 1500.0,
                        'TaxAmount' => 315.0,
                        'TaxPercentage' => 21.0,
                    ],
                    'UUID' => 'd2045e34-49b4-4238-84e2-950362f2007e',
                ],
                [
                    'LineExtensionAmount' => 300.0,
                    'LineExtensionAmountTaxInclusive' => 363.0,
                    'LineItem' => [
                        'CatalogueItemIdentification' => 'BELA',
                        'SellersItemIdentification' => 'stará bela',
                        'Description' => [
                            [
                                'Text' => 'stará bela',
                            ],
                        ],
                        'UnitConversionFactor' => [
                            [
                                'Scheme' => 'Unknown',
                                'UnitCode' => 'Ks',
                                'Value' => 1.0,
                            ],
                            [
                                'Scheme' => 'SI',
                                'UnitCode' => 'm2',
                                'Value' => 2.0,
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
                        'TaxableAmount' => 300.0,
                        'TaxAmount' => 63.0,
                        'TaxPercentage' => 21.0,
                    ],
                    'UUID' => '935f3ea7-3fda-40d8-af8f-a5582fc81f54',
                ],
            ],
            'IssueDate' => '2024-04-02T00:00:00+02:00',
            'MonetaryTotal' => [
                'PayableAmount' => 2178.0,
                'PayableRoundingAmount' => 0.0,
                'TaxExclusiveAmount' => 1800.0,
                'TaxInclusiveAmount' => 2178.0,
            ],
            'PartialDeliveryIndicator' => false,
            'SellerSupplierParty' => [
                'Identifications' => [
                    [
                        'ID' => 'CZ11223344',
                        'Scheme' => 'VAT',
                        'OriginCountry' => 'CZ',
                    ],
                ],
            ],
            'TaxExchangeRate' => [
                'ReferenceCurrencyRate' => 1.0,
                'TaxCurrency' => 'CZK',
                'TaxCurrencyRate' => 1.0,
                'RateDate' => '2024-04-02T00:00:00+02:00',
            ],
            'TaxTotal' => [
                'TaxAmount' => 378.0,
                'TaxSubTotal' => [
                    [
                        'CalculationMethod' => 'Total',
                        'Scheme' => 'Vat',
                        'TaxableAmount' => 1800.0,
                        'TaxAmount' => 378.0,
                        'TaxPercentage' => 21.0,
                    ],
                ],
            ],
            'UUID' => 'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
            'AccountingCustomerParty' => [
                'ContactPerson' => 'Rybana Wassermannová',
                'ElectronicMail' => 'pod.vodnik@zaby.cz',
                'Name' => '1. česká podvodná',
                'PostalAddress' => [
                    'StreetName' => 'Vodná',
                    'BuildingNumber' => '57',
                    'CityName' => 'Žabovřesky',
                    'PostalZone' => '566 00',
                    'CountryIso' => 'CZ',
                ],
                'Telephone' => '+420557788996',
                'Identifications' => [
                    [
                        'ID' => '89745612',
                        'Scheme' => 'UIN',
                        'OriginCountry' => 'CZ',
                    ],
                    [
                        'ID' => 'CZ89745612',
                        'Scheme' => 'VAT',
                        'OriginCountry' => 'CZ',
                    ],
                ],
            ],
            'Delivery' => [
                'ContactPerson' => 'Vodomil Wassermann',
                'ElectronicMail' => 'pod.vodnik@zaby.cz',
                'Name' => '1. česká podvodná',
                'PostalAddress' => [
                    'StreetName' => 'Vodná',
                    'BuildingNumber' => '57',
                    'CityName' => 'Žabovřesky',
                    'PostalZone' => '566 00',
                    'CountryIso' => 'CZ',
                ],
                'Telephone' => '+420557788996',
                'Identifications' => [
                    [
                        'ID' => '89745612',
                        'Scheme' => 'UIN',
                        'OriginCountry' => 'CZ',
                    ],
                    [
                        'ID' => 'CZ89745612',
                        'Scheme' => 'VAT',
                        'OriginCountry' => 'CZ',
                    ],
                ],
            ],
            'ID' => 'eshop0001',
            'PaymentMeansCode' => 'Cheque',
        ], $normalizer->normalize($input));
    }

    public function test_normalize_omits_null_empty_string_and_empty_array_values(): void
    {
        $normalizer = new IncomingOrderInputNormalizer();

        $buyer = (new KnownPartyInput('Buyer'))
            ->withAddress(new PostalAddress(
                street: 'Hlavní',
                buildingNumber: null,
                city: 'Praha',
                postalCode: '11000',
                countryIso: null,
            ));

        $seller = new KnownPartyInput('');

        $lineItem = (new IncomingOrderLineItemInput())
            ->withBuyersItemIdentification('BUY-001');

        $line = new IncomingOrderLineInput(
            uuid: 'line-uuid-1',
            lineExtensionAmount: 100.0,
            lineExtensionAmountTaxInclusive: 121.0,
            lineItem: $lineItem,
            lineQuantity: $this->createQuantity(1.0, 'Ks', DocumentUnitOfMeasureScheme::Unknown),
            taxSubTotal: $this->createTaxSubTotalAdd(100.0, 21.0, 21.0),
            id: null,
            note: '',
        );

        $input = new IncomingOrderInput(
            uuid: 'order-uuid-1',
            issueDate: new DateTimeImmutable('2024-04-02T00:00:00+02:00'),
            currency: Currency::CZK,
            buyerCustomerParty: $buyer,
            sellerSupplierParty: $seller,
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
                rateDate: null,
                exchangeMarketBic: null,
            ),
            taxTotal: new DocumentTaxTotal(
                taxAmount: 21.0,
                taxSubTotals: [
                    $this->createTaxSubTotalTotal(100.0, 21.0, 21.0),
                ],
            ),
            id: null,
            accountingCustomerParty: null,
            delivery: null,
            note: '',
            partialDeliveryIndicator: false,
            paymentMeansCode: null,
        );

        $input->addDocumentLine($line);

        $payload = $normalizer->normalize($input);

        self::assertArrayNotHasKey('AccountingCustomerParty', $payload);
        self::assertArrayNotHasKey('Delivery', $payload);
        self::assertArrayNotHasKey('ID', $payload);
        self::assertArrayNotHasKey('Note', $payload);
        self::assertArrayNotHasKey('PaymentMeansCode', $payload);

        $buyerPayload = $this->requireMap($payload, 'BuyerCustomerParty');
        $buyerAddressPayload = $this->requireMap($buyerPayload, 'PostalAddress');

        self::assertSame([
            'StreetName' => 'Hlavní',
            'CityName' => 'Praha',
            'PostalZone' => '11000',
        ], $buyerAddressPayload);

        self::assertArrayHasKey('SellerSupplierParty', $payload);
        self::assertNull($payload['SellerSupplierParty']);

        $linePayload = $this->requireFirstMapFromList($payload, 'DocumentLine');
        $lineItemPayload = $this->requireMap($linePayload, 'LineItem');
        $taxExchangeRatePayload = $this->requireMap($payload, 'TaxExchangeRate');

        self::assertArrayNotHasKey('Description', $lineItemPayload);
        self::assertArrayNotHasKey('AdditionalAttribute', $lineItemPayload);
        self::assertArrayNotHasKey('UnitConversionFactor', $lineItemPayload);
        self::assertArrayNotHasKey('ID', $linePayload);
        self::assertArrayNotHasKey('Note', $linePayload);
        self::assertArrayNotHasKey('RateDate', $taxExchangeRatePayload);
        self::assertArrayNotHasKey('ExchangeMarketBIC', $taxExchangeRatePayload);

        self::assertSame([
            'BuyersItemIdentification' => 'BUY-001',
        ], $lineItemPayload);
    }

    public function test_normalize_keeps_required_root_keys_and_supports_empty_lines_and_empty_tax_subtotals(): void
    {
        $normalizer = new IncomingOrderInputNormalizer();

        $input = new IncomingOrderInput(
            uuid: 'order-uuid-empty',
            issueDate: new DateTimeImmutable('2024-06-01T00:00:00+02:00'),
            currency: Currency::CZK,
            buyerCustomerParty: new KnownPartyInput('Buyer Only'),
            sellerSupplierParty: new KnownPartyInput(''),
            monetaryTotal: new DocumentMonetaryTotal(
                payableAmount: 0.0,
                payableRoundingAmount: 0.0,
                taxExclusiveAmount: 0.0,
                taxInclusiveAmount: 0.0,
            ),
            taxExchangeRate: new DocumentTaxExchangeRate(
                taxCurrency: Currency::CZK,
                referenceCurrencyRate: 1.0,
                taxCurrencyRate: 1.0,
                rateDate: null,
                exchangeMarketBic: null,
            ),
            taxTotal: new DocumentTaxTotal(
                taxAmount: 0.0,
                taxSubTotals: [],
            ),
            partialDeliveryIndicator: false,
        );

        $payload = $normalizer->normalize($input);

        self::assertSame('order-uuid-empty', $payload['UUID']);
        self::assertSame('CZK', $payload['Currency']);
        self::assertSame('2024-06-01T00:00:00+02:00', $payload['IssueDate']);
        self::assertSame([], $payload['DocumentLine']);
        self::assertFalse($payload['PartialDeliveryIndicator']);

        $taxTotalPayload = $this->requireMap($payload, 'TaxTotal');
        self::assertSame([
            'TaxAmount' => 0.0,
            'TaxSubTotal' => [],
        ], $taxTotalPayload);

        $monetaryTotalPayload = $this->requireMap($payload, 'MonetaryTotal');
        self::assertSame([
            'PayableAmount' => 0.0,
            'PayableRoundingAmount' => 0.0,
            'TaxExclusiveAmount' => 0.0,
            'TaxInclusiveAmount' => 0.0,
        ], $monetaryTotalPayload);

        $buyerPayload = $this->requireMap($payload, 'BuyerCustomerParty');
        self::assertSame([
            'Name' => 'Buyer Only',
        ], $buyerPayload);

        self::assertArrayHasKey('SellerSupplierParty', $payload);
        self::assertNull($payload['SellerSupplierParty']);
    }

    public function test_normalize_includes_optional_description_attribute_and_identification_fields_when_present(): void
    {
        $normalizer = new IncomingOrderInputNormalizer();

        $buyer = (new KnownPartyInput('Buyer'))
            ->addIdentification(new Identification(
                scheme: IdentificationScheme::VAT,
                id: 'CZ12345678',
                originCountry: null,
            ));

        $seller = new KnownPartyInput('Seller');

        $lineItem = (new IncomingOrderLineItemInput())
            ->withStandardItemIdentification('EAN-123')
            ->addDescription(new DocumentDescription('Popis položky', 'cs'))
            ->addAdditionalAttribute(new DocumentAdditionalAttributeInput(
                name: 'Barva',
                value: 'černá',
                attributeKind: DocumentTextualAttributeKind::FreeText,
                langId: 'cs',
                unitCode: 'bal',
                scheme: DocumentUnitOfMeasureScheme::SI,
            ));

        $line = new IncomingOrderLineInput(
            uuid: 'line-optional-1',
            lineExtensionAmount: 50.0,
            lineExtensionAmountTaxInclusive: 60.5,
            lineItem: $lineItem,
            lineQuantity: $this->createQuantity(5.0, 'bal', DocumentUnitOfMeasureScheme::SI),
            taxSubTotal: new DocumentTaxSubTotal(
                calculationMethod: DocumentTaxCalculationMethod::Add,
                scheme: DocumentTaxScheme::Vat,
                taxableAmount: 50.0,
                taxAmount: 10.5,
                taxPercentage: 21.0,
                taxSchemeExtensionCode: 'LOCAL-RC',
            ),
            id: 'ROW-1',
            note: 'Řádková poznámka',
        );

        $input = new IncomingOrderInput(
            uuid: 'order-optional-1',
            issueDate: new DateTimeImmutable('2024-07-01T00:00:00+02:00'),
            currency: Currency::EUR,
            buyerCustomerParty: $buyer,
            sellerSupplierParty: $seller,
            monetaryTotal: new DocumentMonetaryTotal(
                payableAmount: 60.5,
                payableRoundingAmount: 0.0,
                taxExclusiveAmount: 50.0,
                taxInclusiveAmount: 60.5,
            ),
            taxExchangeRate: new DocumentTaxExchangeRate(
                taxCurrency: Currency::EUR,
                referenceCurrencyRate: 1.0,
                taxCurrencyRate: 1.0,
                rateDate: new DateTimeImmutable('2024-07-01T00:00:00+02:00'),
                exchangeMarketBic: 'BANKBIC1',
            ),
            taxTotal: new DocumentTaxTotal(
                taxAmount: 10.5,
                taxSubTotals: [
                    new DocumentTaxSubTotal(
                        calculationMethod: DocumentTaxCalculationMethod::Total,
                        scheme: DocumentTaxScheme::Vat,
                        taxableAmount: 50.0,
                        taxAmount: 10.5,
                        taxPercentage: 21.0,
                        taxSchemeExtensionCode: 'LOCAL-RC',
                    ),
                ],
            ),
            id: 'ORD-1',
            note: 'Hlavičková poznámka',
            partialDeliveryIndicator: true,
            paymentMeansCode: IncomingOrderPaymentMeansCode::BankAccount,
        );

        $input->addDocumentLine($line);

        $payload = $normalizer->normalize($input);

        $buyerPayload = $this->requireMap($payload, 'BuyerCustomerParty');
        $linePayload = $this->requireFirstMapFromList($payload, 'DocumentLine');
        $lineItemPayload = $this->requireMap($linePayload, 'LineItem');
        $lineTaxSubTotalPayload = $this->requireMap($linePayload, 'TaxSubTotal');
        $taxExchangeRatePayload = $this->requireMap($payload, 'TaxExchangeRate');

        self::assertSame([
            [
                'ID' => 'CZ12345678',
                'Scheme' => 'VAT',
            ],
        ], $this->requireList($buyerPayload, 'Identifications'));

        self::assertSame([
            [
                'Text' => 'Popis položky',
                'LangID' => 'cs',
            ],
        ], $this->requireList($lineItemPayload, 'Description'));

        self::assertSame([
            [
                'AttributeKind' => 'FreeText',
                'Name' => 'Barva',
                'Value' => 'černá',
                'LangID' => 'cs',
                'Scheme' => 'SI',
                'UnitCode' => 'bal',
            ],
        ], $this->requireList($lineItemPayload, 'AdditionalAttribute'));

        self::assertSame([
            'CalculationMethod' => 'Add',
            'Scheme' => 'Vat',
            'TaxableAmount' => 50.0,
            'TaxAmount' => 10.5,
            'TaxPercentage' => 21.0,
            'TaxSchemeExtensionCode' => 'LOCAL-RC',
        ], $lineTaxSubTotalPayload);

        self::assertSame([
            'ReferenceCurrencyRate' => 1.0,
            'TaxCurrency' => 'EUR',
            'TaxCurrencyRate' => 1.0,
            'ExchangeMarketBIC' => 'BANKBIC1',
            'RateDate' => '2024-07-01T00:00:00+02:00',
        ], $taxExchangeRatePayload);

        self::assertSame('ORD-1', $payload['ID']);
        self::assertSame('Hlavičková poznámka', $payload['Note']);
        self::assertSame('BankAccount', $payload['PaymentMeansCode']);
        self::assertTrue($payload['PartialDeliveryIndicator']);

        self::assertSame('ROW-1', $linePayload['ID']);
        self::assertSame('Řádková poznámka', $linePayload['Note']);
        self::assertSame('EAN-123', $lineItemPayload['StandardItemIdentification']);
    }

    /**
     * @param list<Identification> $identifications
     */
    private function createParty(
        string $name,
        ?string $contactPerson = null,
        ?string $email = null,
        ?string $telephone = null,
        ?PostalAddress $address = null,
        array $identifications = [],
    ): KnownPartyInput {
        $party = new KnownPartyInput($name);

        if ($contactPerson !== null) {
            $party->withContactPerson($contactPerson);
        }

        if ($email !== null) {
            $party->withEmail($email);
        }

        if ($telephone !== null) {
            $party->withTelephone($telephone);
        }

        if ($address !== null) {
            $party->withAddress($address);
        }

        foreach ($identifications as $identification) {
            $party->addIdentification($identification);
        }

        return $party;
    }

    private function createQuantity(
        float $value,
        string $unitCode,
        DocumentUnitOfMeasureScheme $scheme,
    ): DocumentQuantity {
        return new DocumentQuantity(
            value: $value,
            unitCode: $unitCode,
            scheme: $scheme,
        );
    }

    private function createTaxSubTotalAdd(
        float $taxableAmount,
        float $taxAmount,
        float $taxPercentage,
    ): DocumentTaxSubTotal {
        return new DocumentTaxSubTotal(
            calculationMethod: DocumentTaxCalculationMethod::Add,
            scheme: DocumentTaxScheme::Vat,
            taxableAmount: $taxableAmount,
            taxAmount: $taxAmount,
            taxPercentage: $taxPercentage,
            taxSchemeExtensionCode: null,
        );
    }

    private function createTaxSubTotalTotal(
        float $taxableAmount,
        float $taxAmount,
        float $taxPercentage,
    ): DocumentTaxSubTotal {
        return new DocumentTaxSubTotal(
            calculationMethod: DocumentTaxCalculationMethod::Total,
            scheme: DocumentTaxScheme::Vat,
            taxableAmount: $taxableAmount,
            taxAmount: $taxAmount,
            taxPercentage: $taxPercentage,
            taxSchemeExtensionCode: null,
        );
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    private function requireMap(array $data, string $key): array
    {
        self::assertArrayHasKey($key, $data);

        $value = $data[$key];
        self::assertIsArray($value);

        /** @var array<string,mixed> $value */
        return $value;
    }

    /**
     * @param array<string,mixed> $data
     * @return list<mixed>
     */
    private function requireList(array $data, string $key): array
    {
        self::assertArrayHasKey($key, $data);

        $value = $data[$key];
        self::assertIsArray($value);

        /** @var list<mixed> $value */
        return $value;
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    private function requireFirstMapFromList(array $data, string $key): array
    {
        $list = $this->requireList($data, $key);

        self::assertNotSame([], $list);
        self::assertIsArray($list[0]);

        /** @var array<string,mixed> $row */
        $row = $list[0];

        return $row;
    }
}
