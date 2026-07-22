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
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentAdditionalAttributeValueInput;
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
            name: 'Buyer Company',
            contactPerson: 'Buyer Contact',
            email: 'buyer@example.com',
            telephone: '+420111222333',
            address: new PostalAddress(
                street: 'Water Street',
                buildingNumber: '57',
                city: 'Brno',
                postalCode: '60200',
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
            name: 'Buyer Company',
            contactPerson: 'Accounting Contact',
            email: 'accounting@example.com',
            telephone: '+420111222333',
            address: new PostalAddress(
                street: 'Water Street',
                buildingNumber: '57',
                city: 'Brno',
                postalCode: '60200',
                countryIso: 'CZ',
            ),
            identifications: [
                new Identification(
                    scheme: IdentificationScheme::UIN,
                    id: '89745612',
                    originCountry: 'CZ',
                ),
            ],
        );

        $delivery = $this->createParty(
            name: 'Delivery Company',
            contactPerson: 'Delivery Contact',
            email: 'delivery@example.com',
            telephone: '+420111222333',
            address: new PostalAddress(
                street: 'Water Street',
                buildingNumber: '57',
                city: 'Brno',
                postalCode: '60200',
                countryIso: 'CZ',
            ),
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
            ->withSellersItemIdentification('deravy grosh')
            ->addDescription(new DocumentDescription('deravy grosh'))
            ->addAdditionalAttribute(new DocumentAdditionalAttributeInput(
                name: 'Varianta',
                value: new DocumentAdditionalAttributeValueInput(
                    value: 'velka dira',
                    langId: null,
                    unitCode: null,
                    scheme: DocumentUnitOfMeasureScheme::Unknown,
                ),
                attributeKind: DocumentTextualAttributeKind::ExtendedID,
            ))
            ->addUnitConversionFactor(new DocumentUnitConversionFactor(
                value: 1.0,
                unitCode: 'Ks',
                scheme: DocumentUnitOfMeasureScheme::Unknown,
            ));

        $lineItem2 = (new IncomingOrderLineItemInput())
            ->withCatalogueItemIdentification('BELA')
            ->withSellersItemIdentification('stara bela')
            ->addDescription(new DocumentDescription('stara bela'))
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
            uuid: 'line-1',
            lineExtensionAmount: 1500.0,
            lineExtensionAmountTaxInclusive: 1815.0,
            lineItem: $lineItem1,
            lineQuantity: $this->createQuantity(1.0, 'Ks', DocumentUnitOfMeasureScheme::Unknown),
            taxSubTotal: $this->createTaxSubTotalAdd(1500.0, 315.0, 21.0),
            lineAllowanceAmount: 2400.0,
        );

        $line2 = new IncomingOrderLineInput(
            uuid: 'line-2',
            lineExtensionAmount: 300.0,
            lineExtensionAmountTaxInclusive: 363.0,
            lineItem: $lineItem2,
            lineQuantity: $this->createQuantity(1.0, 'Ks', DocumentUnitOfMeasureScheme::Unknown),
            taxSubTotal: $this->createTaxSubTotalAdd(300.0, 63.0, 21.0),
        );

        $input = new IncomingOrderInput(
            uuid: 'order-1',
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

        $input->addDocumentLine($line1)->addDocumentLine($line2);

        $payload = $normalizer->normalize($input);
        $linePayload = $this->requireFirstMapFromList($payload, 'DocumentLine');
        $lineItemPayload = $this->requireMap($linePayload, 'LineItem');

        self::assertSame(2400.0, $linePayload['LineAllowanceAmount']);
        self::assertSame([
            [
                'AttributeKind' => 'ExtendedID',
                'Name' => 'Varianta',
                'Value' => 'velka dira',
                'Scheme' => 'Unknown',
            ],
        ], $this->requireList($lineItemPayload, 'AdditionalAttribute'));
    }

    public function test_normalize_omits_null_empty_string_and_empty_array_values(): void
    {
        $normalizer = new IncomingOrderInputNormalizer();

        $buyer = (new KnownPartyInput('Buyer'))
            ->withAddress(new PostalAddress(
                street: 'Main',
                buildingNumber: null,
                city: 'Prague',
                postalCode: '11000',
                countryIso: null,
            ));

        $seller = new KnownPartyInput('');
        $lineItem = (new IncomingOrderLineItemInput())->withBuyersItemIdentification('BUY-001');

        $line = new IncomingOrderLineInput(
            uuid: 'line-uuid-1',
            lineExtensionAmount: 100.0,
            lineExtensionAmountTaxInclusive: 121.0,
            lineItem: $lineItem,
            lineQuantity: $this->createQuantity(1.0, 'Ks', DocumentUnitOfMeasureScheme::Unknown),
            taxSubTotal: $this->createTaxSubTotalAdd(100.0, 21.0, 21.0),
            lineAllowanceAmount: null,
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
        $linePayload = $this->requireFirstMapFromList($payload, 'DocumentLine');
        $lineItemPayload = $this->requireMap($linePayload, 'LineItem');

        self::assertArrayNotHasKey('AccountingCustomerParty', $payload);
        self::assertArrayNotHasKey('Delivery', $payload);
        self::assertArrayNotHasKey('ID', $payload);
        self::assertArrayNotHasKey('Note', $payload);
        self::assertArrayNotHasKey('PaymentMeansCode', $payload);
        self::assertArrayNotHasKey('LineAllowanceAmount', $linePayload);
        self::assertSame([
            'BuyersItemIdentification' => 'BUY-001',
        ], $lineItemPayload);
    }

    public function test_normalize_keeps_zero_line_allowance_amount(): void
    {
        $payload = (new IncomingOrderInputNormalizer())->normalize(
            $this->createInputWithAllowance(0.0)
        );

        $linePayload = $this->requireFirstMapFromList($payload, 'DocumentLine');

        self::assertArrayHasKey('LineAllowanceAmount', $linePayload);
        self::assertSame(0.0, $linePayload['LineAllowanceAmount']);
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
            ->addDescription(new DocumentDescription('Item description', 'cs'))
            ->addAdditionalAttribute(new DocumentAdditionalAttributeInput(
                name: 'Color',
                value: new DocumentAdditionalAttributeValueInput(
                    value: 'Black',
                    langId: 'cs',
                    unitCode: 'bal',
                    scheme: DocumentUnitOfMeasureScheme::SI,
                ),
                attributeKind: DocumentTextualAttributeKind::FreeText,
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
            note: 'Line note',
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
            note: 'Header note',
            partialDeliveryIndicator: true,
            paymentMeansCode: IncomingOrderPaymentMeansCode::BankAccount,
        );

        $input->addDocumentLine($line);

        $payload = $normalizer->normalize($input);
        $buyerPayload = $this->requireMap($payload, 'BuyerCustomerParty');
        $linePayload = $this->requireFirstMapFromList($payload, 'DocumentLine');
        $lineItemPayload = $this->requireMap($linePayload, 'LineItem');

        self::assertSame([
            [
                'ID' => 'CZ12345678',
                'Scheme' => 'VAT',
            ],
        ], $this->requireList($buyerPayload, 'Identifications'));

        self::assertSame([
            [
                'Text' => 'Item description',
                'LangID' => 'cs',
            ],
        ], $this->requireList($lineItemPayload, 'Description'));

        self::assertSame([
            [
                'AttributeKind' => 'FreeText',
                'Name' => 'Color',
                'Value' => 'Black',
                'LangID' => 'cs',
                'Scheme' => 'SI',
                'UnitCode' => 'bal',
            ],
        ], $this->requireList($lineItemPayload, 'AdditionalAttribute'));
    }

    private function createInputWithAllowance(?float $allowance): IncomingOrderInput
    {
        $input = new IncomingOrderInput(
            uuid: 'order-zero-allowance',
            issueDate: new DateTimeImmutable('2024-08-01T00:00:00+02:00'),
            currency: Currency::CZK,
            buyerCustomerParty: new KnownPartyInput('Buyer'),
            sellerSupplierParty: new KnownPartyInput('Seller'),
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
            ),
            taxTotal: new DocumentTaxTotal(
                taxAmount: 21.0,
                taxSubTotals: [
                    $this->createTaxSubTotalTotal(100.0, 21.0, 21.0),
                ],
            ),
        );

        $input->addDocumentLine(new IncomingOrderLineInput(
            uuid: 'line-zero-allowance',
            lineExtensionAmount: 100.0,
            lineExtensionAmountTaxInclusive: 121.0,
            lineItem: new IncomingOrderLineItemInput(),
            lineQuantity: $this->createQuantity(1.0, 'Ks', DocumentUnitOfMeasureScheme::Unknown),
            taxSubTotal: $this->createTaxSubTotalAdd(100.0, 21.0, 21.0),
            lineAllowanceAmount: $allowance,
        ));

        return $input;
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
