<?php

declare(strict_types=1);

namespace Lemonade\Vario\Normalizer\IncomingOrder;

use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineItemInput;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentDescription;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentMonetaryTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentQuantity;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxExchangeRate;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxSubTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentUnitConversionFactor;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentAdditionalAttributeInput;
use Lemonade\Vario\Domain\Shared\Identification;
use Lemonade\Vario\Domain\Shared\PostalAddress;

/**
 * Class IncomingOrderInputNormalizer
 *
 * Domain → transport normalizer converting IncomingOrderInput
 * objects into Vario API request payloads.
 *
 * This normalizer follows the documented IncomingOrder example
 * semantically:
 *
 * - fields marked as "neuvádět" are omitted completely
 * - optional fields are sent only when they have a value
 * - postal address is serialized via atomic fields without "Formated"
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Normalizer\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderInputNormalizer
{
    /**
     * @return array<string,mixed>
     */
    public function normalize(IncomingOrderInput $input): array
    {
        $payload = [
            'BuyerCustomerParty' => $this->normalizeParty($input->getBuyerCustomerParty()),
            'Currency' => $input->getCurrency()->value,
            'DocumentLine' => $this->normalizeLines($input->getDocumentLines()),
            'IssueDate' => $input->getIssueDate()->format(DATE_ATOM),
            'MonetaryTotal' => $this->normalizeMonetaryTotal($input->getMonetaryTotal()),
            'PartialDeliveryIndicator' => $input->isPartialDeliveryIndicator(),
            'SellerSupplierParty' => $this->normalizeParty($input->getSellerSupplierParty()),
            'TaxExchangeRate' => $this->normalizeTaxExchangeRate($input->getTaxExchangeRate()),
            'TaxTotal' => $this->normalizeTaxTotal($input->getTaxTotal()),
            'UUID' => $input->getUuid(),
        ];

        $payload += $this->filterNullable([
            'AccountingCustomerParty' => $this->normalizeParty($input->getAccountingCustomerParty()),
            'Delivery' => $this->normalizeParty($input->getDelivery()),
            'ID' => $input->getId(),
            'Note' => $input->getNote(),
            'PaymentMeansCode' => $input->getPaymentMeansCode()?->toApiValue(),
        ]);

        return $payload;
    }

    /**
     * @param list<IncomingOrderLineInput> $lines
     * @return list<array<string,mixed>>
     */
    private function normalizeLines(array $lines): array
    {
        $result = [];

        foreach ($lines as $line) {
            $result[] = $this->normalizeLine($line);
        }

        return $result;
    }

    /**
     * @return array<string,mixed>
     */
    private function normalizeLine(IncomingOrderLineInput $line): array
    {
        $payload = [
            'LineExtensionAmount' => $line->getLineExtensionAmount(),
            'LineExtensionAmountTaxInclusive' => $line->getLineExtensionAmountTaxInclusive(),
            'LineItem' => $this->normalizeLineItem($line->getLineItem()),
            'LineQuantity' => $this->normalizeQuantity($line->getLineQuantity()),
            'TaxSubTotal' => $this->normalizeTaxSubTotal($line->getTaxSubTotal()),
            'UUID' => $line->getUuid(),
        ];

        $payload += $this->filterNullable([
            'ID' => $line->getId(),
            'LineAllowanceAmount' => $line->getLineAllowanceAmount(),
            'Note' => $line->getNote(),
        ]);

        return $payload;
    }

    /**
     * @return array<string,mixed>
     */
    private function normalizeLineItem(IncomingOrderLineItemInput $item): array
    {
        $payload = $this->filterNullable([
            'BuyersItemIdentification' => $item->getBuyersItemIdentification(),
            'CatalogueItemIdentification' => $item->getCatalogueItemIdentification(),
            'SellersItemIdentification' => $item->getSellersItemIdentification(),
            'StandardItemIdentification' => $item->getStandardItemIdentification(),
        ]);

        $descriptions = $this->normalizeDescriptions($item->getDescriptions());
        if ($descriptions !== []) {
            $payload['Description'] = $descriptions;
        }

        $attributes = $this->normalizeAdditionalAttributes($item->getAdditionalAttributes());
        if ($attributes !== []) {
            $payload['AdditionalAttribute'] = $attributes;
        }

        $factors = $this->normalizeUnitConversionFactors($item->getUnitConversionFactors());
        if ($factors !== []) {
            $payload['UnitConversionFactor'] = $factors;
        }

        return $payload;
    }

    /**
     * @return array{
     *     Scheme: string,
     *     UnitCode: string,
     *     Value: float
     * }
     */
    private function normalizeQuantity(DocumentQuantity $quantity): array
    {
        return [
            'Scheme' => $quantity->getScheme()->toApiValue(),
            'UnitCode' => $quantity->getUnitCode(),
            'Value' => $quantity->getValue(),
        ];
    }

    /**
     * @param list<DocumentUnitConversionFactor> $factors
     * @return list<array{
     *     Scheme: string,
     *     UnitCode: string,
     *     Value: float
     * }>
     */
    private function normalizeUnitConversionFactors(array $factors): array
    {
        $result = [];

        foreach ($factors as $factor) {
            $result[] = [
                'Scheme' => $factor->getScheme()->toApiValue(),
                'UnitCode' => $factor->getUnitCode(),
                'Value' => $factor->getValue(),
            ];
        }

        return $result;
    }

    /**
     * @return array{
     *     PayableAmount: float,
     *     PayableRoundingAmount: float,
     *     TaxExclusiveAmount: float,
     *     TaxInclusiveAmount: float
     * }
     */
    private function normalizeMonetaryTotal(DocumentMonetaryTotal $total): array
    {
        return [
            'PayableAmount' => $total->getPayableAmount(),
            'PayableRoundingAmount' => $total->getPayableRoundingAmount(),
            'TaxExclusiveAmount' => $total->getTaxExclusiveAmount(),
            'TaxInclusiveAmount' => $total->getTaxInclusiveAmount(),
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function normalizeTaxSubTotal(DocumentTaxSubTotal $tax): array
    {
        $payload = [
            'CalculationMethod' => $tax->getCalculationMethod()->toApiValue(),
            'Scheme' => $tax->getScheme()->toApiValue(),
            'TaxableAmount' => $tax->getTaxableAmount(),
            'TaxAmount' => $tax->getTaxAmount(),
            'TaxPercentage' => $tax->getTaxPercentage(),
        ];

        $payload += $this->filterNullable([
            'TaxSchemeExtensionCode' => $tax->getTaxSchemeExtensionCode(),
        ]);

        return $payload;
    }

    /**
     * @return array{
     *     TaxAmount: float,
     *     TaxSubTotal: list<array<string,mixed>>
     * }
     */
    private function normalizeTaxTotal(DocumentTaxTotal $taxTotal): array
    {
        $subTotals = [];

        foreach ($taxTotal->getTaxSubTotals() as $subTotal) {
            $subTotals[] = $this->normalizeTaxSubTotal($subTotal);
        }

        return [
            'TaxAmount' => $taxTotal->getTaxAmount(),
            'TaxSubTotal' => $subTotals,
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function normalizeTaxExchangeRate(DocumentTaxExchangeRate $rate): array
    {
        $payload = [
            'ReferenceCurrencyRate' => $rate->getReferenceCurrencyRate(),
            'TaxCurrency' => $rate->getTaxCurrency()->value,
            'TaxCurrencyRate' => $rate->getTaxCurrencyRate(),
        ];

        $payload += $this->filterNullable([
            'ExchangeMarketBIC' => $rate->getExchangeMarketBic(),
            'RateDate' => $rate->getRateDate()?->format(DATE_ATOM),
        ]);

        return $payload;
    }

    /**
     * @return array<string,mixed>|null
     */
    private function normalizeParty(?KnownPartyInput $input): ?array
    {
        if ($input === null) {
            return null;
        }

        $payload = $this->filterNullable([
            'ContactPerson' => $input->getContactPerson(),
            'ElectronicMail' => $input->getEmail(),
            'Name' => $input->getName() !== '' ? $input->getName() : null,
            'PostalAddress' => $this->normalizeAddress($input->getAddress()),
            'Telephone' => $input->getTelephone(),
        ]);

        $identifications = $this->normalizeIdentifications($input->getIdentifications());

        if ($identifications !== []) {
            $payload['Identifications'] = $identifications;
        }

        return $payload === [] ? null : $payload;
    }

    /**
     * @return array{
     *     StreetName?: string,
     *     BuildingNumber?: string,
     *     CityName?: string,
     *     PostalZone?: string,
     *     CountryIso?: string
     * }|null
     */
    private function normalizeAddress(?PostalAddress $address): ?array
    {
        if ($address === null) {
            return null;
        }

        $a = $address->toArray();

        $payload = [];

        if ($a['street'] !== null && $a['street'] !== '') {
            $payload['StreetName'] = $a['street'];
        }

        if ($a['buildingNumber'] !== null && $a['buildingNumber'] !== '') {
            $payload['BuildingNumber'] = $a['buildingNumber'];
        }

        if ($a['city'] !== null && $a['city'] !== '') {
            $payload['CityName'] = $a['city'];
        }

        if ($a['postalCode'] !== null && $a['postalCode'] !== '') {
            $payload['PostalZone'] = $a['postalCode'];
        }

        if ($a['countryIso'] !== null && $a['countryIso'] !== '') {
            $payload['CountryIso'] = $a['countryIso'];
        }

        return $payload === [] ? null : $payload;
    }

    /**
     * @param list<Identification> $identifications
     * @return list<array<string,mixed>>
     */
    private function normalizeIdentifications(array $identifications): array
    {
        $result = [];

        foreach ($identifications as $id) {
            $row = [
                'ID' => $id->getId(),
                'Scheme' => $id->getScheme()->value,
            ];

            $row += $this->filterNullable([
                'OriginCountry' => $id->getOriginCountry(),
            ]);

            $result[] = $row;
        }

        return $result;
    }

    /**
     * @param list<DocumentDescription> $descriptions
     * @return list<array<string,mixed>>
     */
    private function normalizeDescriptions(array $descriptions): array
    {
        $result = [];

        foreach ($descriptions as $description) {
            $row = [
                'Text' => $description->getText(),
            ];

            $row += $this->filterNullable([
                'LangID' => $description->getLangId(),
            ]);

            $result[] = $row;
        }

        return $result;
    }

    /**
     * @param list<DocumentAdditionalAttributeInput> $attributes
     * @return list<array<string,mixed>>
     */
    private function normalizeAdditionalAttributes(array $attributes): array
    {
        $result = [];

        foreach ($attributes as $attribute) {
            $row = [
                'AttributeKind' => $attribute->getAttributeKind()->toApiValue(),
                'Name' => $attribute->getName(),
                'Value' => $attribute->getValue(),
            ];

            $row += $this->filterNullable([
                'LangID' => $attribute->getLangId(),
                'Scheme' => $attribute->getScheme()?->toApiValue(),
                'UnitCode' => $attribute->getUnitCode(),
            ]);

            $result[] = $row;
        }

        return $result;
    }

    /**
     * Removes null, empty-string and empty-array values.
     *
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    private function filterNullable(array $data): array
    {
        return array_filter(
            $data,
            static fn($v): bool => $v !== null && $v !== '' && $v !== [],
        );
    }
}
