<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Mapper\IncomingOrder;

use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPaymentMeansCode;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxCalculationMethod;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxScheme;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTextualAttributeKind;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;
use Lemonade\Vario\Mapper\IncomingOrder\IncomingOrderMapper;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

final class IncomingOrderMapperTest extends TestCase
{
    public function test_map_returns_fully_mapped_order_from_complete_payload(): void
    {
        $mapper = new IncomingOrderMapper();

        $order = $mapper->map([
            'UUID' => 'order-uuid-1',
            'ID' => 'ORD-1',
            'IssueDate' => '2025-05-06T00:00:00+01:00',
            'Currency' => 'CZK',
            'Note' => 'Objednávka poznámka',
            'PartialDeliveryIndicator' => 1,
            'PaymentMeansCode' => 'BankAccount',

            'BuyerCustomerParty' => [
                'Name' => 'KARLOMIX TRADE s.r.o. - stavebniny',
                'ContactPerson' => 'Jan Dvořák',
                'ElectronicMail' => 'dvorak@karlomix.cz',
                'Telephone' => '608120126',
                'PostalAddress' => [
                    'StreetName' => 'Rosnice',
                    'BuildingNumber' => '63',
                    'CityName' => 'KARLOVY VARY',
                    'PostalZone' => '360 17',
                    'CountryIso' => 'CZ',
                ],
                'Identifications' => [
                    [
                        'Scheme' => 'UIN',
                        'ID' => '25214721',
                        'OriginCountry' => 'CZ',
                    ],
                    [
                        'Scheme' => 'VAT',
                        'ID' => 'CZ25214721',
                        'OriginCountry' => 'CZ',
                    ],
                    [
                        'Scheme' => 'UUID',
                        'ID' => '58c95897-600e-4294-8d8c-af6606c8a1f9',
                        'OriginCountry' => 'CZ',
                    ],
                    [
                        'Scheme' => 'ISID',
                        'ID' => 'KARLOMIX TRADE s.r.o.',
                        'OriginCountry' => 'CZ',
                    ],
                    [
                        'Scheme' => 'UNKNOWN',
                        'ID' => 'ignored',
                        'OriginCountry' => 'CZ',
                    ],
                ],
                'PartyExtra' => 'buyer-extra',
            ],

            'AccountingCustomerParty' => [
                'Name' => 'KARLOMIX TRADE s.r.o. - stavebniny',
                'ElectronicMail' => 'fakturace@karlomix.cz',
                'Telephone' => '777888999',
                'PostalAddress' => [
                    'StreetName' => 'Rosnice',
                    'BuildingNumber' => '63',
                    'CityName' => 'KARLOVY VARY',
                    'PostalZone' => '360 17',
                    'CountryIso' => 'CZ',
                ],
                'Identifications' => [
                    [
                        'Scheme' => 'VAT',
                        'ID' => 'CZ25214721',
                        'OriginCountry' => 'CZ',
                    ],
                ],
            ],

            'Delivery' => [
                'Name' => 'KARLOMIX TRADE s.r.o. - sklad',
                'ContactPerson' => 'Skladník',
                'ElectronicMail' => 'sklad@karlomix.cz',
                'Telephone' => '111222333',
                'PostalAddress' => [
                    'StreetName' => 'Rosnice',
                    'BuildingNumber' => '63',
                    'CityName' => 'KARLOVY VARY',
                    'PostalZone' => '360 17',
                    'CountryIso' => 'CZ',
                ],
                'Identifications' => [
                    [
                        'Scheme' => 'UIN',
                        'ID' => '25214721',
                        'OriginCountry' => 'CZ',
                    ],
                ],
            ],

            'SellerSupplierParty' => [
                'Name' => 'Krby TURBO s.r.o.',
                'ElectronicMail' => 'objednavky@krbyturbo.cz',
                'Telephone' => '731513328',
                'PostalAddress' => [
                    'StreetName' => 'Neklanova',
                    'BuildingNumber' => '149/36',
                    'CityName' => 'Praha 2',
                    'PostalZone' => '128 00',
                    'CountryIso' => 'CZ',
                ],
                'Identifications' => [
                    [
                        'Scheme' => 'UIN',
                        'ID' => '27126714',
                        'OriginCountry' => 'CZ',
                    ],
                    [
                        'Scheme' => 'VAT',
                        'ID' => 'CZ27126714',
                        'OriginCountry' => 'CZ',
                    ],
                ],
            ],

            'DocumentLine' => [
                [
                    'UUID' => 'line-uuid-1',
                    'ID' => '1',
                    'LineExtensionAmount' => 11680.5,
                    'LineExtensionAmountTaxInclusive' => 14133.4,
                    'Note' => 'Řádková poznámka',
                    'LineItem' => [
                        'BuyersItemIdentification' => 'BUY-1',
                        'CatalogueItemIdentification' => 'M500-1000610-30',
                        'SellersItemIdentification' => 'Vd3.',
                        'StandardItemIdentification' => 'EAN-1',
                        'Description' => [
                            [
                                'Text' => 'Thermax ECO 1000x610x30 mm',
                                'LangID' => 'cs',
                            ],
                            [
                                'LangID' => 'en',
                            ],
                        ],
                        'AdditionalAttribute' => [
                            [
                                'AttributeKind' => 'ExtendedID',
                                'Name' => 'Varianta',
                                'Value' => 'šedá',
                                'LangID' => 'cs',
                                'UnitCode' => 'bal',
                                'Scheme' => 'SI',
                                'AdditionalExtra' => 'extra-a',
                            ],
                            [
                                'AttributeKind' => 'ExtendedID',
                                'Name' => 'Broken',
                            ],
                        ],
                        'TextualAttribute' => [
                            [
                                'AttributeKind' => 'ExtendedID',
                                'Name' => 'Sklad',
                                'Value' => 'Mlýnek',
                                'LangID' => 'cs',
                                'TextExtra' => 'x',
                            ],
                            [
                                'AttributeKind' => 'INVALID',
                                'Name' => 'Ignored',
                                'Value' => 'ignored',
                            ],
                        ],
                        'NumericAttribute' => [
                            [
                                'AttributeKind' => 'FreeNumeric',
                                'Name' => 'Pocet_polozky_1',
                                'Value' => 5,
                                'UnitCode' => 'm2',
                                'NumericExtra' => 'n',
                            ],
                            [
                                'AttributeKind' => 'INVALID',
                                'Name' => 'Ignored',
                                'Value' => 10,
                            ],
                        ],
                        'UnitConversionFactor' => [
                            [
                                'Scheme' => 'Unknown',
                                'UnitCode' => 'ks',
                                'Value' => 1,
                            ],
                            [
                                'Scheme' => 'SI',
                                'UnitCode' => 'm2',
                                'Value' => 2,
                            ],
                            [
                                'Scheme' => 'SI',
                                'Value' => 999,
                            ],
                        ],
                        'UnexpectedNested' => 'line-item-extra',
                    ],
                    'LineQuantity' => [
                        'Scheme' => 'Unknown',
                        'UnitCode' => 'ks',
                        'Value' => 30,
                    ],
                    'TaxSubTotal' => [
                        'CalculationMethod' => 'Add',
                        'Scheme' => 'Vat',
                        'TaxableAmount' => 11680.5,
                        'TaxAmount' => 2452.9,
                        'TaxPercentage' => 21,
                        'TaxSchemeExtensionCode' => 'LOCAL-RC',
                    ],
                    'UnitPrice' => [
                        'Amount' => 389.35,
                        'AmountTaxInclusive' => 471.11,
                        'Quantity' => [
                            'Scheme' => 'Unknown',
                            'UnitCode' => 'ks',
                            'Value' => 1,
                        ],
                        'Discount' => 5,
                    ],
                    'LineExtra' => 'line-extra',
                ],
                [
                    'UUID' => 'line-uuid-2',
                    'LineExtensionAmount' => 250.0,
                    'LineExtensionAmountTaxInclusive' => 302.5,
                    'LineItem' => [
                        'Description' => [
                            [
                                'Text' => 'Paleta 120x80cm',
                            ],
                        ],
                    ],
                ],
            ],

            'MonetaryTotal' => [
                'PayableAmount' => 15101.4,
                'PayableRoundingAmount' => 0,
                'TaxExclusiveAmount' => 12480.5,
                'TaxInclusiveAmount' => 15101.4,
            ],

            'TaxExchangeRate' => [
                'TaxCurrency' => 'CZK',
                'ReferenceCurrencyRate' => 1,
                'TaxCurrencyRate' => 1,
                'RateDate' => '2025-05-06T00:00:00+01:00',
                'ExchangeMarketBIC' => 'CNBACZPP',
            ],

            'TaxTotal' => [
                'TaxAmount' => 2620.9,
                'TaxSubTotal' => [
                    [
                        'CalculationMethod' => 'Total',
                        'Scheme' => 'Vat',
                        'TaxableAmount' => 12480.5,
                        'TaxAmount' => 2620.9,
                        'TaxPercentage' => 21,
                    ],
                    [
                        'CalculationMethod' => 'INVALID',
                        'Scheme' => 'Vat',
                        'TaxableAmount' => 1,
                        'TaxAmount' => 1,
                        'TaxPercentage' => 1,
                    ],
                ],
            ],

            'DeliveryDetail' => [
                'OptionCode' => [
                    'raben 7.5.2025',
                    null,
                    123,
                    'druhá možnost',
                ],
                'RequestedDeliveryDate' => '2025-05-07T00:00:00+01:00',
                'Carrier' => 'Raben',
            ],

            'TextualAttribute' => [
                [
                    'AttributeKind' => 'ExtendedID',
                    'Name' => 'Cislo_dokladu',
                    'Value' => 'D-25-02102',
                    'LangID' => 'cs',
                    'RootExtra' => 'root-x',
                ],
                [
                    'AttributeKind' => 'INVALID',
                    'Name' => 'Ignored',
                    'Value' => 'x',
                ],
            ],

            'RootExtra' => [
                'foo' => 'bar',
            ],
        ]);

        self::assertSame('order-uuid-1', $order->getUuid());
        self::assertSame('ORD-1', $order->getId());
        self::assertSame('2025-05-06T00:00:00+01:00', $order->getIssueDate()?->format(DATE_ATOM));
        self::assertSame(Currency::CZK, $order->getCurrency());
        self::assertSame('Objednávka poznámka', $order->getNote());
        self::assertTrue($order->getPartialDeliveryIndicator());
        self::assertSame(IncomingOrderPaymentMeansCode::BankAccount, $order->getPaymentMeansCode());

        $buyer = $order->getBuyerCustomerParty();
        self::assertNotNull($buyer);
        self::assertSame('KARLOMIX TRADE s.r.o. - stavebniny', $buyer->getName());
        self::assertSame('Jan Dvořák', $buyer->getContactPerson());
        self::assertSame('dvorak@karlomix.cz', $buyer->getEmail());
        self::assertSame('608120126', $buyer->getTelephone());
        self::assertTrue($buyer->hasAddress());
        self::assertTrue($buyer->hasIdentifications());
        self::assertSame('25214721', $buyer->getCompanyNumber());
        self::assertSame('CZ25214721', $buyer->getVatId());
        self::assertSame(['PartyExtra' => 'buyer-extra'], $buyer->getExtra());

        $seller = $order->getSellerSupplierParty();
        self::assertNotNull($seller);
        self::assertSame('Krby TURBO s.r.o.', $seller->getName());
        self::assertSame('CZ27126714', $seller->getVatId());

        $accounting = $order->getAccountingCustomerParty();
        self::assertNotNull($accounting);
        self::assertSame('fakturace@karlomix.cz', $accounting->getEmail());

        $delivery = $order->getDelivery();
        self::assertNotNull($delivery);
        self::assertSame('Skladník', $delivery->getContactPerson());

        self::assertTrue($order->hasDocumentLines());
        self::assertCount(2, $order->getDocumentLines());

        $line1 = $order->getDocumentLines()[0];
        self::assertSame('line-uuid-1', $line1->getUuid());
        self::assertSame('1', $line1->getId());
        self::assertSame(11680.5, $line1->getLineExtensionAmount());
        self::assertSame(14133.4, $line1->getLineExtensionAmountTaxInclusive());
        self::assertSame('Řádková poznámka', $line1->getNote());
        self::assertTrue($line1->hasLineItem());
        self::assertTrue($line1->hasQuantity());
        self::assertTrue($line1->hasTaxSubTotal());
        self::assertTrue($line1->hasUnitPrice());
        self::assertSame(['LineExtra' => 'line-extra'], $line1->getExtra());

        $line1Item = $line1->getLineItem();
        self::assertNotNull($line1Item);
        self::assertSame('BUY-1', $line1Item->getBuyersItemIdentification());
        self::assertSame('M500-1000610-30', $line1Item->getCatalogueItemIdentification());
        self::assertSame('Vd3.', $line1Item->getSellersItemIdentification());
        self::assertSame('EAN-1', $line1Item->getStandardItemIdentification());
        self::assertTrue($line1Item->hasAnyProductIdentification());

        self::assertTrue($line1Item->hasDescriptions());
        self::assertCount(1, $line1Item->getDescriptions());

        $primaryDescription = $line1Item->getPrimaryDescription();
        self::assertNotNull($primaryDescription);
        self::assertSame('Thermax ECO 1000x610x30 mm', $primaryDescription->getText());
        self::assertSame('cs', $primaryDescription->getLangId());

        self::assertTrue($line1Item->hasAdditionalAttributes());
        self::assertCount(1, $line1Item->getAdditionalAttributes());
        $additionalAttribute = $line1Item->getAdditionalAttributes()[0];
        self::assertSame(DocumentTextualAttributeKind::ExtendedID, $additionalAttribute->getAttributeKind());
        self::assertSame('Varianta', $additionalAttribute->getName());
        self::assertSame('šedá', $additionalAttribute->getValue());
        self::assertSame('cs', $additionalAttribute->getLangId());
        self::assertSame('bal', $additionalAttribute->getUnitCode());
        self::assertSame(DocumentUnitOfMeasureScheme::SI, $additionalAttribute->getScheme());
        self::assertSame(['AdditionalExtra' => 'extra-a'], $additionalAttribute->getExtra());

        self::assertTrue($line1Item->hasTextualAttributes());
        self::assertCount(1, $line1Item->getTextualAttributes());
        $textualAttribute = $line1Item->getTextualAttributes()[0];
        self::assertSame(DocumentTextualAttributeKind::ExtendedID, $textualAttribute->getAttributeKind());
        self::assertSame('Sklad', $textualAttribute->getName());
        self::assertSame('Mlýnek', $textualAttribute->getValue());
        self::assertSame('cs', $textualAttribute->getLangId());
        self::assertSame(['TextExtra' => 'x'], $textualAttribute->getExtra());

        self::assertTrue($line1Item->hasNumericAttributes());
        self::assertCount(1, $line1Item->getNumericAttributes());
        $numericAttribute = $line1Item->getNumericAttributes()[0];
        self::assertSame('Pocet_polozky_1', $numericAttribute->getName());
        self::assertSame(5.0, $numericAttribute->getValue());
        self::assertSame('m2', $numericAttribute->getUnitCode());
        self::assertSame(['NumericExtra' => 'n'], $numericAttribute->getExtra());

        self::assertTrue($line1Item->hasUnitConversionFactors());
        self::assertCount(2, $line1Item->getUnitConversionFactors());
        self::assertSame(1.0, $line1Item->getUnitConversionFactors()[0]->getValue());
        self::assertSame('ks', $line1Item->getUnitConversionFactors()[0]->getUnitCode());
        self::assertSame(DocumentUnitOfMeasureScheme::Unknown, $line1Item->getUnitConversionFactors()[0]->getScheme());
        self::assertSame(2.0, $line1Item->getUnitConversionFactors()[1]->getValue());
        self::assertSame('m2', $line1Item->getUnitConversionFactors()[1]->getUnitCode());
        self::assertSame(DocumentUnitOfMeasureScheme::SI, $line1Item->getUnitConversionFactors()[1]->getScheme());

        self::assertSame([
            'UnexpectedNested' => 'line-item-extra',
        ], $line1Item->getExtra());

        $line1Quantity = $line1->getLineQuantity();
        self::assertNotNull($line1Quantity);
        self::assertSame(30.0, $line1Quantity->getValue());
        self::assertSame('ks', $line1Quantity->getUnitCode());
        self::assertSame(DocumentUnitOfMeasureScheme::Unknown, $line1Quantity->getScheme());

        $line1Tax = $line1->getTaxSubTotal();
        self::assertNotNull($line1Tax);
        self::assertSame(DocumentTaxCalculationMethod::Add, $line1Tax->getCalculationMethod());
        self::assertSame(DocumentTaxScheme::Vat, $line1Tax->getScheme());
        self::assertSame(11680.5, $line1Tax->getTaxableAmount());
        self::assertSame(2452.9, $line1Tax->getTaxAmount());
        self::assertSame(21.0, $line1Tax->getTaxPercentage());
        self::assertSame('LOCAL-RC', $line1Tax->getTaxSchemeExtensionCode());

        $line1UnitPrice = $line1->getUnitPrice();
        self::assertNotNull($line1UnitPrice);
        self::assertSame(389.35, $line1UnitPrice->getAmount());
        self::assertSame(471.11, $line1UnitPrice->getAmountTaxInclusive());

        $line1UnitPriceQuantity = $line1UnitPrice->getQuantity();
        self::assertNotNull($line1UnitPriceQuantity);
        self::assertSame('ks', $line1UnitPriceQuantity->getUnitCode());

        self::assertSame([
            'Discount' => 5,
        ], $line1UnitPrice->getExtra());

        $line2 = $order->getDocumentLines()[1];
        self::assertSame('line-uuid-2', $line2->getUuid());
        self::assertNull($line2->getId());
        self::assertTrue($line2->hasLineItem());
        self::assertFalse($line2->hasQuantity());
        self::assertFalse($line2->hasTaxSubTotal());
        self::assertFalse($line2->hasUnitPrice());

        $line2Item = $line2->getLineItem();
        self::assertNotNull($line2Item);
        self::assertSame('Paleta 120x80cm', $line2Item->getPrimaryDescriptionText());

        self::assertTrue($order->hasMonetaryTotal());
        $monetaryTotal = $order->getMonetaryTotal();
        self::assertNotNull($monetaryTotal);
        self::assertSame(15101.4, $monetaryTotal->getPayableAmount());
        self::assertSame(0.0, $monetaryTotal->getPayableRoundingAmount());
        self::assertSame(12480.5, $monetaryTotal->getTaxExclusiveAmount());
        self::assertSame(15101.4, $monetaryTotal->getTaxInclusiveAmount());

        $taxExchangeRate = $order->getTaxExchangeRate();
        self::assertNotNull($taxExchangeRate);
        self::assertSame(Currency::CZK, $taxExchangeRate->getTaxCurrency());
        self::assertSame(1.0, $taxExchangeRate->getReferenceCurrencyRate());
        self::assertSame(1.0, $taxExchangeRate->getTaxCurrencyRate());
        self::assertSame('2025-05-06T00:00:00+01:00', $taxExchangeRate->getRateDate()?->format(DATE_ATOM));
        self::assertSame('CNBACZPP', $taxExchangeRate->getExchangeMarketBic());

        self::assertTrue($order->hasTaxTotal());
        $taxTotal = $order->getTaxTotal();
        self::assertNotNull($taxTotal);
        self::assertSame(2620.9, $taxTotal->getTaxAmount());
        self::assertCount(1, $taxTotal->getTaxSubTotals());
        self::assertSame(
            DocumentTaxCalculationMethod::Total,
            $taxTotal->getTaxSubTotals()[0]->getCalculationMethod(),
        );

        self::assertTrue($order->hasDeliveryDetail());
        $deliveryDetail = $order->getDeliveryDetail();
        self::assertNotNull($deliveryDetail);
        self::assertSame(
            ['raben 7.5.2025', '123', 'druhá možnost'],
            $deliveryDetail->getOptionCodes(),
        );
        self::assertSame(
            '2025-05-07T00:00:00+01:00',
            $deliveryDetail->getRequestedDeliveryDate()?->format(DATE_ATOM),
        );
        self::assertSame([
            'Carrier' => 'Raben',
        ], $deliveryDetail->getExtra());

        self::assertTrue($order->hasTextualAttributes());
        self::assertCount(1, $order->getTextualAttributes());
        self::assertSame('Cislo_dokladu', $order->getTextualAttributes()[0]->getName());
        self::assertSame('D-25-02102', $order->getTextualAttributes()[0]->getValue());
        self::assertSame('cs', $order->getTextualAttributes()[0]->getLangId());
        self::assertSame([
            'RootExtra' => 'root-x',
        ], $order->getTextualAttributes()[0]->getExtra());

        self::assertSame([
            'RootExtra' => [
                'foo' => 'bar',
            ],
        ], $order->getExtra());
    }

    public function test_map_handles_sparse_and_invalid_nested_payloads_gracefully(): void
    {
        $mapper = new IncomingOrderMapper();

        $order = $mapper->map([
            'UUID' => 'order-uuid-sparse',
            'IssueDate' => 'not-a-date',
            'Currency' => 'INVALID',
            'PartialDeliveryIndicator' => 'maybe',
            'PaymentMeansCode' => 'INVALID',

            'BuyerCustomerParty' => 'not-an-array',
            'AccountingCustomerParty' => [],
            'Delivery' => [
                ['Name' => 'ignored because numeric keys'],
            ],

            'SellerSupplierParty' => [
                'Name' => 'Sparse seller',
                'PostalAddress' => [
                    'StreetName' => '',
                    'BuildingNumber' => null,
                    'CityName' => '',
                    'PostalZone' => '',
                    'CountryIso' => null,
                ],
                'Identifications' => [
                    [
                        'Scheme' => null,
                        'ID' => 'x',
                    ],
                    [
                        'Scheme' => 'INVALID',
                        'ID' => 'x',
                    ],
                    [
                        'Scheme' => 'VAT',
                        'ID' => null,
                    ],
                ],
            ],

            'DocumentLine' => [
                [
                    'UUID' => 'line-sparse-1',
                    'LineExtensionAmount' => '10.5',
                    'LineExtensionAmountTaxInclusive' => '12.71',
                    'LineItem' => [
                        'Description' => [
                            [
                                'LangID' => 'cs',
                            ],
                        ],
                        'AdditionalAttribute' => [
                            [
                                'Name' => 'Broken',
                            ],
                            [
                                'Name' => 'Fallback',
                                'Value' => 'yes',
                            ],
                        ],
                        'TextualAttribute' => [
                            [
                                'AttributeKind' => 'INVALID',
                                'Name' => 'Ignored',
                                'Value' => 'x',
                            ],
                        ],
                        'NumericAttribute' => [
                            [
                                'AttributeKind' => 'INVALID',
                                'Name' => 'Ignored',
                                'Value' => 1,
                            ],
                        ],
                        'UnitConversionFactor' => [
                            [
                                'Value' => 1,
                            ],
                        ],
                        'UnexpectedNested' => 'nested-extra',
                    ],
                    'LineQuantity' => [
                        'Scheme' => 'Unknown',
                        'Value' => 1,
                    ],
                    'TaxSubTotal' => [
                        'CalculationMethod' => 'INVALID',
                        'Scheme' => 'Vat',
                        'TaxableAmount' => 1,
                        'TaxAmount' => 2,
                        'TaxPercentage' => 3,
                    ],
                    'UnitPrice' => [
                        'Amount' => 10.5,
                        'AmountTaxInclusive' => 12.71,
                        'Quantity' => [
                            0 => 'oops',
                        ],
                        'UnitPriceExtra' => 'u',
                    ],
                ],
            ],

            'MonetaryTotal' => [
                'PayableAmount' => 1,
                'PayableRoundingAmount' => 0,
                'TaxExclusiveAmount' => 1,
            ],

            'TaxExchangeRate' => [
                'TaxCurrency' => 'CZK',
                'ReferenceCurrencyRate' => 1,
                'TaxCurrencyRate' => 1,
                'RateDate' => 'not-a-date',
                'ExchangeMarketBIC' => 'BICX',
            ],

            'TaxTotal' => [
                'TaxAmount' => 5,
                'TaxSubTotal' => [
                    [
                        'CalculationMethod' => 'INVALID',
                        'Scheme' => 'Vat',
                        'TaxableAmount' => 1,
                        'TaxAmount' => 2,
                        'TaxPercentage' => 3,
                    ],
                ],
            ],

            'DeliveryDetail' => [
                'OptionCode' => [
                    'ok',
                    null,
                    7,
                    'next',
                ],
                'RequestedDeliveryDate' => 'not-a-date',
                'Other' => 'extra',
            ],

            'TextualAttribute' => [
                [
                    'AttributeKind' => 'FreeText',
                    'Name' => 'DocNo',
                    'Value' => 'D-1',
                    'LangID' => 'cs',
                    'X' => 'y',
                ],
                [
                    'AttributeKind' => 'BAD',
                    'Name' => 'Ignored',
                    'Value' => 'x',
                ],
            ],

            'RootExtra' => 'root-extra',
        ]);

        self::assertSame('order-uuid-sparse', $order->getUuid());
        self::assertNull($order->getIssueDate());
        self::assertNull($order->getCurrency());
        self::assertNull($order->getPartialDeliveryIndicator());
        self::assertNull($order->getPaymentMeansCode());

        self::assertNull($order->getBuyerCustomerParty());
        self::assertNull($order->getAccountingCustomerParty());
        self::assertNull($order->getDelivery());

        $seller = $order->getSellerSupplierParty();
        self::assertNotNull($seller);
        self::assertSame('Sparse seller', $seller->getName());
        self::assertFalse($seller->hasAddress());
        self::assertFalse($seller->hasIdentifications());
        self::assertNull($seller->getPostalAddress());
        self::assertNull($seller->getCompanyNumber());
        self::assertNull($seller->getVatId());

        self::assertCount(1, $order->getDocumentLines());
        $line = $order->getDocumentLines()[0];

        self::assertSame('line-sparse-1', $line->getUuid());
        self::assertSame(10.5, $line->getLineExtensionAmount());
        self::assertSame(12.71, $line->getLineExtensionAmountTaxInclusive());
        self::assertNull($line->getLineQuantity());
        self::assertNull($line->getTaxSubTotal());

        $lineItem = $line->getLineItem();
        self::assertNotNull($lineItem);
        self::assertFalse($lineItem->hasDescriptions());
        self::assertSame([], $lineItem->getDescriptions());

        self::assertTrue($lineItem->hasAdditionalAttributes());
        self::assertCount(1, $lineItem->getAdditionalAttributes());
        self::assertSame(
            DocumentTextualAttributeKind::ExtendedID,
            $lineItem->getAdditionalAttributes()[0]->getAttributeKind(),
        );
        self::assertSame('Fallback', $lineItem->getAdditionalAttributes()[0]->getName());
        self::assertSame('yes', $lineItem->getAdditionalAttributes()[0]->getValue());
        self::assertNull($lineItem->getAdditionalAttributes()[0]->getScheme());

        self::assertFalse($lineItem->hasTextualAttributes());
        self::assertFalse($lineItem->hasNumericAttributes());
        self::assertFalse($lineItem->hasUnitConversionFactors());

        self::assertSame([
            'UnexpectedNested' => 'nested-extra',
        ], $lineItem->getExtra());

        $unitPrice = $line->getUnitPrice();
        self::assertNotNull($unitPrice);
        self::assertSame(10.5, $unitPrice->getAmount());
        self::assertSame(12.71, $unitPrice->getAmountTaxInclusive());
        self::assertNull($unitPrice->getQuantity());
        self::assertSame([
            'UnitPriceExtra' => 'u',
        ], $unitPrice->getExtra());

        self::assertFalse($order->hasMonetaryTotal());
        self::assertNull($order->getMonetaryTotal());

        $taxExchangeRate = $order->getTaxExchangeRate();
        self::assertNotNull($taxExchangeRate);
        self::assertSame(Currency::CZK, $taxExchangeRate->getTaxCurrency());
        self::assertNull($taxExchangeRate->getRateDate());
        self::assertSame('BICX', $taxExchangeRate->getExchangeMarketBic());

        $taxTotal = $order->getTaxTotal();
        self::assertNotNull($taxTotal);
        self::assertSame(5.0, $taxTotal->getTaxAmount());
        self::assertSame([], $taxTotal->getTaxSubTotals());

        $deliveryDetail = $order->getDeliveryDetail();
        self::assertNotNull($deliveryDetail);
        self::assertSame(['ok', '7', 'next'], $deliveryDetail->getOptionCodes());
        self::assertNull($deliveryDetail->getRequestedDeliveryDate());
        self::assertSame([
            'Other' => 'extra',
        ], $deliveryDetail->getExtra());

        self::assertTrue($order->hasTextualAttributes());
        self::assertCount(1, $order->getTextualAttributes());
        self::assertSame(DocumentTextualAttributeKind::FreeText, $order->getTextualAttributes()[0]->getAttributeKind());
        self::assertSame('DocNo', $order->getTextualAttributes()[0]->getName());
        self::assertSame('D-1', $order->getTextualAttributes()[0]->getValue());
        self::assertSame('cs', $order->getTextualAttributes()[0]->getLangId());
        self::assertSame(['X' => 'y'], $order->getTextualAttributes()[0]->getExtra());

        self::assertSame([
            'RootExtra' => 'root-extra',
        ], $order->getExtra());
    }

    public function test_map_returns_null_for_invalid_top_level_optional_structures(): void
    {
        $mapper = new IncomingOrderMapper();

        $order = $mapper->map([
            'UUID' => 'order-null-branches',
            'BuyerCustomerParty' => [],
            'AccountingCustomerParty' => [],
            'Delivery' => [],
            'SellerSupplierParty' => [],
            'DocumentLine' => [
                [
                    'UUID' => 'line-minimal',
                    'LineExtensionAmount' => 1,
                    'LineExtensionAmountTaxInclusive' => 1.21,
                ],
            ],
            'MonetaryTotal' => [],
            'TaxExchangeRate' => [
                'TaxCurrency' => 'INVALID',
                'ReferenceCurrencyRate' => 1,
                'TaxCurrencyRate' => 1,
            ],
            'TaxTotal' => [],
            'DeliveryDetail' => [],
            'TextualAttribute' => [],
        ]);

        self::assertNull($order->getBuyerCustomerParty());
        self::assertNull($order->getAccountingCustomerParty());
        self::assertNull($order->getDelivery());
        self::assertNull($order->getSellerSupplierParty());

        self::assertCount(1, $order->getDocumentLines());
        self::assertFalse($order->getDocumentLines()[0]->hasLineItem());
        self::assertFalse($order->getDocumentLines()[0]->hasQuantity());
        self::assertFalse($order->getDocumentLines()[0]->hasTaxSubTotal());
        self::assertFalse($order->getDocumentLines()[0]->hasUnitPrice());

        self::assertFalse($order->hasMonetaryTotal());
        self::assertNull($order->getMonetaryTotal());

        self::assertNull($order->getTaxExchangeRate());

        self::assertFalse($order->hasTaxTotal());
        self::assertNull($order->getTaxTotal());

        self::assertFalse($order->hasDeliveryDetail());
        self::assertNull($order->getDeliveryDetail());

        self::assertFalse($order->hasTextualAttributes());
        self::assertSame([], $order->getTextualAttributes());
    }

    public function test_map_throws_when_uuid_is_missing(): void
    {
        $mapper = new IncomingOrderMapper();

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IncomingOrder payload missing required field "UUID".');

        $mapper->map([
            'ID' => 'missing-uuid',
        ]);
    }

    /**
     * @dataProvider partialDeliveryIndicatorProvider
     */
    public function test_map_handles_partial_delivery_indicator_variants(
        mixed $rawValue,
        ?bool $expected,
    ): void {
        $mapper = new IncomingOrderMapper();

        $order = $mapper->map([
            'UUID' => 'bool-case',
            'PartialDeliveryIndicator' => $rawValue,
        ]);

        self::assertSame($expected, $order->getPartialDeliveryIndicator());
    }

    public function test_map_returns_null_tax_total_when_tax_amount_is_missing(): void
    {
        $mapper = new IncomingOrderMapper();

        $order = $mapper->map([
            'UUID' => 'order-tax-total-null',
            'TaxTotal' => [
                'TaxSubTotal' => [
                    [
                        'CalculationMethod' => 'Total',
                        'Scheme' => 'Vat',
                        'TaxableAmount' => 100.0,
                        'TaxAmount' => 21.0,
                        'TaxPercentage' => 21.0,
                    ],
                ],
            ],
        ]);

        self::assertFalse($order->hasTaxTotal());
        self::assertNull($order->getTaxTotal());
    }

    public function test_map_returns_null_tax_total_when_tax_amount_is_invalid(): void
    {
        $mapper = new IncomingOrderMapper();

        $order = $mapper->map([
            'UUID' => 'order-tax-total-invalid',
            'TaxTotal' => [
                'TaxAmount' => 'not-a-number',
                'TaxSubTotal' => [
                    [
                        'CalculationMethod' => 'Total',
                        'Scheme' => 'Vat',
                        'TaxableAmount' => 100.0,
                        'TaxAmount' => 21.0,
                        'TaxPercentage' => 21.0,
                    ],
                ],
            ],
        ]);

        self::assertFalse($order->hasTaxTotal());
        self::assertNull($order->getTaxTotal());
    }

    /**
     * @return iterable<string,array{0:mixed,1:?bool}>
     */
    public static function partialDeliveryIndicatorProvider(): iterable
    {
        yield 'bool true' => [true, true];
        yield 'bool false' => [false, false];
        yield 'int 1' => [1, true];
        yield 'int 0' => [0, false];
        yield 'string 1' => ['1', true];
        yield 'string 0' => ['0', false];
        yield 'string true' => ['true', true];
        yield 'string false' => ['false', false];
        yield 'string yes' => ['yes', true];
        yield 'string no' => ['no', false];
        yield 'string invalid' => ['maybe', null];
    }
}
