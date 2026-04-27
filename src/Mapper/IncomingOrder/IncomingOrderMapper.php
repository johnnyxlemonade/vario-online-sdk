<?php

declare(strict_types=1);

namespace Lemonade\Vario\Mapper\IncomingOrder;

use DateTimeImmutable;
use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderNumericAttributeKind;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPaymentMeansCode;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTaxCalculationMethod;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTaxScheme;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTextualAttributeKind;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderUnitOfMeasureScheme;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrder;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderAdditionalAttribute;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderDeliveryDetail;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderDescription;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderLine;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderLineItem;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderNumericAttribute;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderParty;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderTextualAttribute;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderUnitPrice;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderMonetaryTotal;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderQuantity;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderTaxExchangeRate;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderTaxSubTotal;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderTaxTotal;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderUnitConversionFactor;
use Lemonade\Vario\Domain\Shared\Identification;
use Lemonade\Vario\Domain\Shared\IdentificationCollection;
use Lemonade\Vario\Domain\Shared\IdentificationScheme;
use Lemonade\Vario\Domain\Shared\PostalAddress;
use Lemonade\Vario\Mapper\Support\ScalarReadersTrait;
use Throwable;
use UnexpectedValueException;

/**
 * Class IncomingOrderMapper
 *
 * Transport → Domain mapper converting raw Vario API payloads
 * into strongly typed IncomingOrder domain objects.
 *
 * The mapper acts as an anti-corruption layer between the external
 * Vario API transport format and the internal SDK domain model.
 * It is responsible for:
 *
 * - extracting known fields from the API payload
 * - normalizing scalar values
 * - constructing nested read models and value objects
 * - preserving unknown fields for forward compatibility
 *
 * Any fields not explicitly mapped are stored in the `$extra`
 * payload of the resulting domain object.
 *
 * The mapper is intended to be used internally by:
 *
 *     IncomingOrderApi::query()
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Mapper\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderMapper
{
    use ScalarReadersTrait;

    /**
     * @param array<string,mixed> $data
     */
    public function map(array $data): IncomingOrder
    {
        /** ---------- Parties ---------- */
        $buyer = $this->mapParty($this->arrayOrNull($data['BuyerCustomerParty'] ?? null));
        $accounting = $this->mapParty($this->arrayOrNull($data['AccountingCustomerParty'] ?? null));
        $delivery = $this->mapParty($this->arrayOrNull($data['Delivery'] ?? null));
        $seller = $this->mapParty($this->arrayOrNull($data['SellerSupplierParty'] ?? null));

        /** ---------- Lines ---------- */
        $linesRaw = $data['DocumentLine'] ?? null;

        /** @var array<int,array<string,mixed>> $linesData */
        $linesData = is_array($linesRaw) ? $linesRaw : [];

        $documentLines = $this->mapLines($linesData);

        /** ---------- Totals ---------- */
        $monetaryTotal = $this->mapMonetaryTotal(
            $this->arrayOrNull($data['MonetaryTotal'] ?? null)
        );

        $taxExchangeRate = $this->mapTaxExchangeRate(
            $this->arrayOrNull($data['TaxExchangeRate'] ?? null)
        );

        $taxTotal = $this->mapTaxTotal(
            $this->arrayOrNull($data['TaxTotal'] ?? null)
        );

        /** ---------- Delivery detail ---------- */
        $deliveryDetail = $this->mapDeliveryDetail(
            $this->arrayOrNull($data['DeliveryDetail'] ?? null)
        );

        /** ---------- Top-level textual attributes ---------- */
        $textualAttributesRaw = $data['TextualAttribute'] ?? null;

        /** @var array<int,array<string,mixed>> $textualAttributesData */
        $textualAttributesData = is_array($textualAttributesRaw)
            ? $textualAttributesRaw
            : [];

        /** ---------- Forward compatibility payload ---------- */
        /** @var array<string,mixed> $extra */
        $extra = array_diff_key($data, [
            'UUID' => true,
            'ID' => true,
            'IssueDate' => true,
            'Currency' => true,
            'Note' => true,
            'PartialDeliveryIndicator' => true,
            'PaymentMeansCode' => true,
            'BuyerCustomerParty' => true,
            'AccountingCustomerParty' => true,
            'Delivery' => true,
            'SellerSupplierParty' => true,
            'DocumentLine' => true,
            'MonetaryTotal' => true,
            'TaxExchangeRate' => true,
            'TaxTotal' => true,
            'DeliveryDetail' => true,
            'TextualAttribute' => true,
        ]);

        return new IncomingOrder(
            uuid: $this->readRequiredString($data, 'UUID'),
            id: $this->readString($data, 'ID'),
            issueDate: $this->readDateTime($data, 'IssueDate'),
            currency: Currency::tryFromNullable($this->readString($data, 'Currency')),
            note: $this->readString($data, 'Note'),
            partialDeliveryIndicator: $this->readBool($data, 'PartialDeliveryIndicator'),
            paymentMeansCode: $this->readPaymentMeansCode($data),
            buyerCustomerParty: $buyer,
            accountingCustomerParty: $accounting,
            delivery: $delivery,
            sellerSupplierParty: $seller,
            documentLines: $documentLines,
            monetaryTotal: $monetaryTotal,
            taxExchangeRate: $taxExchangeRate,
            taxTotal: $taxTotal,
            deliveryDetail: $deliveryDetail,
            textualAttributes: $this->mapTextualAttributes($textualAttributesData),
            extra: $extra,
        );
    }

    /**
     * @param array<string,mixed>|null $data
     */
    private function mapParty(?array $data): ?IncomingOrderParty
    {
        if ($data === null || $data === []) {
            return null;
        }

        $address = $this->mapAddress(
            $this->arrayOrNull($data['PostalAddress'] ?? null)
        );

        $identificationsRaw = $data['Identifications'] ?? null;

        /** @var array<int,array<string,mixed>> $identificationsData */
        $identificationsData = is_array($identificationsRaw)
            ? $identificationsRaw
            : [];

        $identifications = $this->mapIdentifications($identificationsData);

        /** @var array<string,mixed> $extra */
        $extra = array_diff_key($data, [
            'Name' => true,
            'ContactPerson' => true,
            'ElectronicMail' => true,
            'Telephone' => true,
            'PostalAddress' => true,
            'Identifications' => true,
        ]);

        return new IncomingOrderParty(
            name: $this->readString($data, 'Name'),
            contactPerson: $this->readString($data, 'ContactPerson'),
            email: $this->readString($data, 'ElectronicMail'),
            telephone: $this->readString($data, 'Telephone'),
            postalAddress: $address,
            identifications: $identifications,
            extra: $extra,
        );
    }

    /**
     * @param array<int,array<string,mixed>> $rows
     * @return list<IncomingOrderLine>
     */
    private function mapLines(array $rows): array
    {
        $mapped = [];

        foreach ($rows as $row) {
            $mapped[] = $this->mapLine($row);
        }

        return $mapped;
    }

    /**
     * @param array<string,mixed> $data
     */
    private function mapLine(array $data): IncomingOrderLine
    {
        $lineItem = $this->mapLineItem(
            $this->arrayOrNull($data['LineItem'] ?? null)
        );

        $lineQuantity = $this->mapQuantity(
            $this->arrayOrNull($data['LineQuantity'] ?? null)
        );

        $taxSubTotal = $this->mapTaxSubTotal(
            $this->arrayOrNull($data['TaxSubTotal'] ?? null)
        );

        $unitPrice = $this->mapUnitPrice(
            $this->arrayOrNull($data['UnitPrice'] ?? null)
        );

        /** @var array<string,mixed> $extra */
        $extra = array_diff_key($data, [
            'UUID' => true,
            'ID' => true,
            'LineExtensionAmount' => true,
            'LineExtensionAmountTaxInclusive' => true,
            'Note' => true,
            'LineItem' => true,
            'LineQuantity' => true,
            'TaxSubTotal' => true,
            'UnitPrice' => true,
        ]);

        return new IncomingOrderLine(
            uuid: $this->readString($data, 'UUID'),
            id: $this->readString($data, 'ID'),
            lineExtensionAmount: $this->readFloat($data, 'LineExtensionAmount'),
            lineExtensionAmountTaxInclusive: $this->readFloat($data, 'LineExtensionAmountTaxInclusive'),
            note: $this->readString($data, 'Note'),
            lineItem: $lineItem,
            lineQuantity: $lineQuantity,
            taxSubTotal: $taxSubTotal,
            unitPrice: $unitPrice,
            extra: $extra,
        );
    }

    /**
     * @param array<string,mixed>|null $data
     */
    private function mapLineItem(?array $data): ?IncomingOrderLineItem
    {
        if ($data === null || $data === []) {
            return null;
        }

        $descriptionsRaw = $data['Description'] ?? null;
        $additionalAttributesRaw = $data['AdditionalAttribute'] ?? null;
        $textualAttributesRaw = $data['TextualAttribute'] ?? null;
        $numericAttributesRaw = $data['NumericAttribute'] ?? null;
        $factorsRaw = $data['UnitConversionFactor'] ?? null;

        /** @var array<int,array<string,mixed>> $descriptionsData */
        $descriptionsData = is_array($descriptionsRaw) ? $descriptionsRaw : [];

        /** @var array<int,array<string,mixed>> $additionalAttributesData */
        $additionalAttributesData = is_array($additionalAttributesRaw) ? $additionalAttributesRaw : [];

        /** @var array<int,array<string,mixed>> $textualAttributesData */
        $textualAttributesData = is_array($textualAttributesRaw) ? $textualAttributesRaw : [];

        /** @var array<int,array<string,mixed>> $numericAttributesData */
        $numericAttributesData = is_array($numericAttributesRaw) ? $numericAttributesRaw : [];

        /** @var array<int,array<string,mixed>> $factorsData */
        $factorsData = is_array($factorsRaw) ? $factorsRaw : [];

        /** @var array<string,mixed> $extra */
        $extra = array_diff_key($data, [
            'BuyersItemIdentification' => true,
            'CatalogueItemIdentification' => true,
            'SellersItemIdentification' => true,
            'StandardItemIdentification' => true,
            'Description' => true,
            'AdditionalAttribute' => true,
            'TextualAttribute' => true,
            'NumericAttribute' => true,
            'UnitConversionFactor' => true,
        ]);

        return new IncomingOrderLineItem(
            buyersItemIdentification: $this->readString($data, 'BuyersItemIdentification'),
            catalogueItemIdentification: $this->readString($data, 'CatalogueItemIdentification'),
            sellersItemIdentification: $this->readString($data, 'SellersItemIdentification'),
            standardItemIdentification: $this->readString($data, 'StandardItemIdentification'),
            descriptions: $this->mapDescriptions($descriptionsData),
            additionalAttributes: $this->mapAdditionalAttributes($additionalAttributesData),
            textualAttributes: $this->mapTextualAttributes($textualAttributesData),
            numericAttributes: $this->mapNumericAttributes($numericAttributesData),
            unitConversionFactors: $this->mapUnitConversionFactors($factorsData),
            extra: $extra,
        );
    }

    /**
     * @param array<int,array<string,mixed>> $rows
     * @return list<IncomingOrderDescription>
     */
    private function mapDescriptions(array $rows): array
    {
        $mapped = [];

        foreach ($rows as $row) {
            $text = $this->stringOrNull($row['Text'] ?? null);

            if ($text === null) {
                continue;
            }

            $mapped[] = new IncomingOrderDescription(
                text: $text,
                langId: $this->stringOrNull($row['LangID'] ?? null),
            );
        }

        return $mapped;
    }

    /**
     * @param array<int,array<string,mixed>> $rows
     * @return list<IncomingOrderAdditionalAttribute>
     */
    private function mapAdditionalAttributes(array $rows): array
    {
        $mapped = [];

        foreach ($rows as $row) {
            $name = $this->stringOrNull($row['Name'] ?? null);
            $value = $this->stringOrNull($row['Value'] ?? null);

            if ($name === null || $value === null) {
                continue;
            }

            $kind = IncomingOrderTextualAttributeKind::tryFrom(
                $this->stringOrNull($row['AttributeKind'] ?? null) ?? 'ExtendedID'
            ) ?? IncomingOrderTextualAttributeKind::ExtendedID;

            $scheme = IncomingOrderUnitOfMeasureScheme::tryFrom(
                $this->stringOrNull($row['Scheme'] ?? null) ?? ''
            );

            /** @var array<string,mixed> $extra */
            $extra = array_diff_key($row, [
                'AttributeKind' => true,
                'LangID' => true,
                'Name' => true,
                'Scheme' => true,
                'UnitCode' => true,
                'Value' => true,
            ]);

            $mapped[] = new IncomingOrderAdditionalAttribute(
                attributeKind: $kind,
                name: $name,
                value: $value,
                langId: $this->stringOrNull($row['LangID'] ?? null),
                unitCode: $this->stringOrNull($row['UnitCode'] ?? null),
                scheme: $scheme,
                extra: $extra,
            );
        }

        return $mapped;
    }

    /**
     * @param array<int,array<string,mixed>> $rows
     * @return list<IncomingOrderTextualAttribute>
     */
    private function mapTextualAttributes(array $rows): array
    {
        $mapped = [];

        foreach ($rows as $row) {
            $kind = IncomingOrderTextualAttributeKind::tryFrom(
                $this->stringOrNull($row['AttributeKind'] ?? null) ?? ''
            );

            if ($kind === null) {
                continue;
            }

            /** @var array<string,mixed> $extra */
            $extra = array_diff_key($row, [
                'AttributeKind' => true,
                'Name' => true,
                'Value' => true,
                'LangID' => true,
            ]);

            $mapped[] = new IncomingOrderTextualAttribute(
                attributeKind: $kind,
                name: $this->stringOrNull($row['Name'] ?? null),
                value: $this->stringOrNull($row['Value'] ?? null),
                langId: $this->stringOrNull($row['LangID'] ?? null),
                extra: $extra,
            );
        }

        return $mapped;
    }

    /**
     * @param array<int,array<string,mixed>> $rows
     * @return list<IncomingOrderNumericAttribute>
     */
    private function mapNumericAttributes(array $rows): array
    {
        $mapped = [];

        foreach ($rows as $row) {
            $kind = IncomingOrderNumericAttributeKind::tryFrom(
                $this->stringOrNull($row['AttributeKind'] ?? null) ?? ''
            );

            if ($kind === null) {
                continue;
            }

            /** @var array<string,mixed> $extra */
            $extra = array_diff_key($row, [
                'AttributeKind' => true,
                'Name' => true,
                'Value' => true,
                'UnitCode' => true,
            ]);

            $mapped[] = new IncomingOrderNumericAttribute(
                attributeKind: $kind,
                name: $this->stringOrNull($row['Name'] ?? null),
                value: $this->floatOrNull($row['Value'] ?? null),
                unitCode: $this->stringOrNull($row['UnitCode'] ?? null),
                extra: $extra,
            );
        }

        return $mapped;
    }

    /**
     * @param array<int,array<string,mixed>> $rows
     * @return list<IncomingOrderUnitConversionFactor>
     */
    private function mapUnitConversionFactors(array $rows): array
    {
        $mapped = [];

        foreach ($rows as $row) {
            $value = $this->floatOrNull($row['Value'] ?? null);
            $unitCode = $this->stringOrNull($row['UnitCode'] ?? null);

            if ($value === null || $unitCode === null) {
                continue;
            }

            $scheme = IncomingOrderUnitOfMeasureScheme::tryFrom(
                $this->stringOrNull($row['Scheme'] ?? null) ?? 'Unknown'
            ) ?? IncomingOrderUnitOfMeasureScheme::Unknown;

            $mapped[] = new IncomingOrderUnitConversionFactor(
                value: $value,
                unitCode: $unitCode,
                scheme: $scheme,
            );
        }

        return $mapped;
    }

    /**
     * @param array<string,mixed>|null $data
     */
    private function mapQuantity(?array $data): ?IncomingOrderQuantity
    {
        if ($data === null || $data === []) {
            return null;
        }

        $value = $this->floatOrNull($data['Value'] ?? null);
        $unitCode = $this->stringOrNull($data['UnitCode'] ?? null);

        if ($value === null || $unitCode === null) {
            return null;
        }

        $scheme = IncomingOrderUnitOfMeasureScheme::tryFrom(
            $this->stringOrNull($data['Scheme'] ?? null) ?? 'Unknown'
        ) ?? IncomingOrderUnitOfMeasureScheme::Unknown;

        return new IncomingOrderQuantity(
            value: $value,
            unitCode: $unitCode,
            scheme: $scheme,
        );
    }

    /**
     * @param array<string,mixed>|null $data
     */
    private function mapMonetaryTotal(?array $data): ?IncomingOrderMonetaryTotal
    {
        if ($data === null || $data === []) {
            return null;
        }

        $payableAmount = $this->floatOrNull($data['PayableAmount'] ?? null);
        $payableRoundingAmount = $this->floatOrNull($data['PayableRoundingAmount'] ?? null);
        $taxExclusiveAmount = $this->floatOrNull($data['TaxExclusiveAmount'] ?? null);
        $taxInclusiveAmount = $this->floatOrNull($data['TaxInclusiveAmount'] ?? null);

        if (
            $payableAmount === null
            || $payableRoundingAmount === null
            || $taxExclusiveAmount === null
            || $taxInclusiveAmount === null
        ) {
            return null;
        }

        return new IncomingOrderMonetaryTotal(
            payableAmount: $payableAmount,
            payableRoundingAmount: $payableRoundingAmount,
            taxExclusiveAmount: $taxExclusiveAmount,
            taxInclusiveAmount: $taxInclusiveAmount,
        );
    }

    /**
     * @param array<string,mixed>|null $data
     */
    private function mapTaxSubTotal(?array $data): ?IncomingOrderTaxSubTotal
    {
        if ($data === null || $data === []) {
            return null;
        }

        $calculationMethod = IncomingOrderTaxCalculationMethod::tryFrom(
            $this->stringOrNull($data['CalculationMethod'] ?? null) ?? ''
        );

        $scheme = IncomingOrderTaxScheme::tryFrom(
            $this->stringOrNull($data['Scheme'] ?? null) ?? ''
        );

        $taxableAmount = $this->floatOrNull($data['TaxableAmount'] ?? null);
        $taxAmount = $this->floatOrNull($data['TaxAmount'] ?? null);
        $taxPercentage = $this->floatOrNull($data['TaxPercentage'] ?? null);

        if (
            $calculationMethod === null
            || $scheme === null
            || $taxableAmount === null
            || $taxAmount === null
            || $taxPercentage === null
        ) {
            return null;
        }

        return new IncomingOrderTaxSubTotal(
            calculationMethod: $calculationMethod,
            scheme: $scheme,
            taxableAmount: $taxableAmount,
            taxAmount: $taxAmount,
            taxPercentage: $taxPercentage,
            taxSchemeExtensionCode: $this->stringOrNull($data['TaxSchemeExtensionCode'] ?? null),
        );
    }

    /**
     * @param array<string,mixed>|null $data
     */
    private function mapUnitPrice(?array $data): ?IncomingOrderUnitPrice
    {
        if ($data === null || $data === []) {
            return null;
        }

        $quantity = $this->mapQuantity(
            $this->arrayOrNull($data['Quantity'] ?? null)
        );

        /** @var array<string,mixed> $extra */
        $extra = array_diff_key($data, [
            'Amount' => true,
            'AmountTaxInclusive' => true,
            'Quantity' => true,
        ]);

        return new IncomingOrderUnitPrice(
            amount: $this->readFloat($data, 'Amount'),
            amountTaxInclusive: $this->readFloat($data, 'AmountTaxInclusive'),
            quantity: $quantity,
            extra: $extra,
        );
    }

    /**
     * @param array<string,mixed>|null $data
     */
    private function mapDeliveryDetail(?array $data): ?IncomingOrderDeliveryDetail
    {
        if ($data === null || $data === []) {
            return null;
        }

        $optionCodesRaw = $data['OptionCode'] ?? null;
        $optionCodes = [];

        if (is_array($optionCodesRaw)) {
            foreach ($optionCodesRaw as $value) {
                $item = $this->stringOrNull($value);

                if ($item !== null) {
                    $optionCodes[] = $item;
                }
            }
        }

        /** @var array<string,mixed> $extra */
        $extra = array_diff_key($data, [
            'OptionCode' => true,
            'RequestedDeliveryDate' => true,
        ]);

        return new IncomingOrderDeliveryDetail(
            optionCodes: $optionCodes,
            requestedDeliveryDate: $this->readDateTime($data, 'RequestedDeliveryDate'),
            extra: $extra,
        );
    }

    /**
     * @param array<string,mixed>|null $data
     */
    private function mapTaxTotal(?array $data): ?IncomingOrderTaxTotal
    {
        if ($data === null || $data === []) {
            return null;
        }

        $taxAmount = $this->floatOrNull($data['TaxAmount'] ?? null);

        $subTotalsRaw = $data['TaxSubTotal'] ?? null;

        /** @var array<int,array<string,mixed>> $subTotalsData */
        $subTotalsData = is_array($subTotalsRaw) ? $subTotalsRaw : [];

        $subTotals = [];

        foreach ($subTotalsData as $row) {
            $mapped = $this->mapTaxSubTotal($row);

            if ($mapped !== null) {
                $subTotals[] = $mapped;
            }
        }

        if ($taxAmount === null) {
            return null;
        }

        return new IncomingOrderTaxTotal(
            taxAmount: $taxAmount,
            taxSubTotals: $subTotals,
        );
    }

    /**
     * @param array<string,mixed>|null $data
     */
    private function mapTaxExchangeRate(?array $data): ?IncomingOrderTaxExchangeRate
    {
        if ($data === null || $data === []) {
            return null;
        }

        $taxCurrency = Currency::tryFromNullable(
            $this->stringOrNull($data['TaxCurrency'] ?? null)
        );

        $referenceCurrencyRate = $this->floatOrNull($data['ReferenceCurrencyRate'] ?? null);
        $taxCurrencyRate = $this->floatOrNull($data['TaxCurrencyRate'] ?? null);

        if (
            $taxCurrency === null
            || $referenceCurrencyRate === null
            || $taxCurrencyRate === null
        ) {
            return null;
        }

        return new IncomingOrderTaxExchangeRate(
            taxCurrency: $taxCurrency,
            referenceCurrencyRate: $referenceCurrencyRate,
            taxCurrencyRate: $taxCurrencyRate,
            rateDate: $this->readDateTime($data, 'RateDate'),
            exchangeMarketBic: $this->readString($data, 'ExchangeMarketBIC'),
        );
    }

    /**
     * @param array<string,mixed>|null $data
     */
    private function mapAddress(?array $data): ?PostalAddress
    {
        if ($data === null || $data === []) {
            return null;
        }

        $street = $this->stringOrNull($data['StreetName'] ?? null);
        $buildingNumber = $this->stringOrNull($data['BuildingNumber'] ?? null);
        $city = $this->stringOrNull($data['CityName'] ?? null);
        $postalCode = $this->stringOrNull($data['PostalZone'] ?? null);
        $countryIso = $this->stringOrNull($data['CountryIso'] ?? null);

        if (
            $street === null
            && $buildingNumber === null
            && $city === null
            && $postalCode === null
            && $countryIso === null
        ) {
            return null;
        }

        return new PostalAddress(
            street: $street,
            buildingNumber: $buildingNumber,
            city: $city,
            postalCode: $postalCode,
            countryIso: $countryIso,
        );
    }

    /**
     * @param array<int,array<string,mixed>> $items
     */
    private function mapIdentifications(array $items): IdentificationCollection
    {
        $mapped = [];

        foreach ($items as $item) {
            $schemeValue = $this->stringOrNull($item['Scheme'] ?? null);
            $id = $this->stringOrNull($item['ID'] ?? null);

            if ($schemeValue === null || $id === null) {
                continue;
            }

            $scheme = IdentificationScheme::tryFrom($schemeValue);

            if ($scheme === null) {
                continue;
            }

            $mapped[] = new Identification(
                scheme: $scheme,
                id: $id,
                originCountry: $this->stringOrNull($item['OriginCountry'] ?? null),
            );
        }

        return new IdentificationCollection($mapped);
    }

    /**
     * @param array<string,mixed> $data
     */
    private function readString(array $data, string $key): ?string
    {
        return $this->stringOrNull($data[$key] ?? null);
    }

    /**
     * @param array<string,mixed> $data
     */
    private function readRequiredString(array $data, string $key): string
    {
        $value = $this->readString($data, $key);

        if ($value === null) {
            throw new UnexpectedValueException(
                sprintf('IncomingOrder payload missing required field "%s".', $key)
            );
        }

        return $value;
    }

    /**
     * @param array<string,mixed> $data
     */
    private function readFloat(array $data, string $key): ?float
    {
        return $this->floatOrNull($data[$key] ?? null);
    }

    /**
     * @param array<string,mixed> $data
     */
    private function readDateTime(array $data, string $key): ?DateTimeImmutable
    {
        $value = $this->readString($data, $key);

        if ($value === null) {
            return null;
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @param array<string,mixed> $data
     */
    private function readPaymentMeansCode(array $data): ?IncomingOrderPaymentMeansCode
    {
        $value = $this->readString($data, 'PaymentMeansCode');

        return $value !== null
            ? IncomingOrderPaymentMeansCode::tryFrom($value)
            : null;
    }

    /**
     * @param array<string,mixed> $data
     */
    private function readBool(array $data, string $key): ?bool
    {
        $value = $data[$key] ?? null;

        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $value === 1;
        }

        if (is_string($value)) {
            $normalized = strtolower(trim($value));

            return match ($normalized) {
                '1', 'true', 'yes' => true,
                '0', 'false', 'no' => false,
                default => null,
            };
        }

        return null;
    }

    /**
     * @param mixed $value
     * @return array<string,mixed>|null
     */
    private function arrayOrNull(mixed $value): ?array
    {
        if (!is_array($value)) {
            return null;
        }

        $result = [];

        foreach ($value as $key => $item) {
            if (!is_string($key)) {
                return null;
            }

            $result[$key] = $item;
        }

        return $result;
    }
}
