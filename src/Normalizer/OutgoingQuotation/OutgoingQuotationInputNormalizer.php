<?php

declare(strict_types=1);

namespace Lemonade\Vario\Normalizer\OutgoingQuotation;

use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationLineInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationLineItemInput;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentDescription;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentMonetaryTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentQuantity;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxExchangeRate;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxSubTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxTotal;
use Lemonade\Vario\Domain\Shared\Identification;
use Lemonade\Vario\Domain\Shared\PostalAddress;

final class OutgoingQuotationInputNormalizer
{
    /**
     * @return array<string,mixed>
     */
    public function normalize(OutgoingQuotationInput $input): array
    {
        $payload = [
            'BuyerCustomerParty' => $this->normalizeParty($input->getBuyerCustomerParty()),
            'SellerSupplierParty' => $this->normalizeParty($input->getSellerSupplierParty()),
        ];

        $id = $input->getId();
        if ($id !== null && $id !== '') {
            $payload['ID'] = $id;
        }

        $payload['UUID'] = $input->getUuid();
        $payload['IssueDate'] = $input->getIssueDate()->format(DATE_ATOM);
        $payload['Currency'] = $input->getCurrency()->value;

        $paymentMeansCode = $input->getPaymentMeansCode()?->toApiValue();
        if ($paymentMeansCode !== null && $paymentMeansCode !== '') {
            $payload['PaymentMeansCode'] = $paymentMeansCode;
        }

        $payload['DocumentLine'] = $this->normalizeLines($input->getDocumentLines());
        $payload['MonetaryTotal'] = $this->normalizeMonetaryTotal($input->getMonetaryTotal());
        $payload['TaxExchangeRate'] = $this->normalizeTaxExchangeRate($input->getTaxExchangeRate());
        $payload['TaxTotal'] = $this->normalizeTaxTotal($input->getTaxTotal());

        $note = $input->getNote();
        if ($note !== null && $note !== '') {
            $payload['Note'] = $note;
        }

        return $payload;
    }

    /**
     * @param list<OutgoingQuotationLineInput> $lines
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
    private function normalizeLine(OutgoingQuotationLineInput $line): array
    {
        $payload = [];

        $id = $line->getId();
        if ($id !== null && $id !== '') {
            $payload['ID'] = $id;
        }

        $payload['UUID'] = $line->getUuid();
        $payload['LineExtensionAmount'] = $line->getLineExtensionAmount();

        $lineAllowanceAmount = $line->getLineAllowanceAmount();
        if ($lineAllowanceAmount !== null) {
            $payload['LineAllowanceAmount'] = $lineAllowanceAmount;
        }

        $payload['LineExtensionAmountTaxInclusive'] = $line->getLineExtensionAmountTaxInclusive();
        $payload['LineItem'] = $this->normalizeLineItem($line->getLineItem());
        $payload['LineQuantity'] = $this->normalizeQuantity($line->getLineQuantity());
        $payload['TaxSubTotal'] = $this->normalizeTaxSubTotal($line->getTaxSubTotal());

        $note = $line->getNote();
        if ($note !== null && $note !== '') {
            $payload['Note'] = $note;
        }

        return $payload;
    }

    /**
     * @return array<string,mixed>
     */
    private function normalizeLineItem(OutgoingQuotationLineItemInput $item): array
    {
        $payload = [];

        $buyersItemIdentification = $item->getBuyersItemIdentification();
        if ($buyersItemIdentification !== null && $buyersItemIdentification !== '') {
            $payload['BuyersItemIdentification'] = $buyersItemIdentification;
        }

        $catalogueItemIdentification = $item->getCatalogueItemIdentification();
        if ($catalogueItemIdentification !== null && $catalogueItemIdentification !== '') {
            $payload['CatalogueItemIdentification'] = $catalogueItemIdentification;
        }

        $sellersItemIdentification = $item->getSellersItemIdentification();
        if ($sellersItemIdentification !== null && $sellersItemIdentification !== '') {
            $payload['SellersItemIdentification'] = $sellersItemIdentification;
        }

        $standardItemIdentification = $item->getStandardItemIdentification();
        if ($standardItemIdentification !== null && $standardItemIdentification !== '') {
            $payload['StandardItemIdentification'] = $standardItemIdentification;
        }

        $descriptions = $this->normalizeDescriptions($item->getDescriptions());

        if ($descriptions !== []) {
            $payload['Description'] = $descriptions;
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
            'TaxExclusiveAmount' => $total->getTaxExclusiveAmount(),
            'TaxInclusiveAmount' => $total->getTaxInclusiveAmount(),
            'PayableRoundingAmount' => $total->getPayableRoundingAmount(),
            'PayableAmount' => $total->getPayableAmount(),
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

        return $payload + $this->filterNullable([
            'TaxSchemeExtensionCode' => $tax->getTaxSchemeExtensionCode(),
        ]);
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

        return $payload + $this->filterNullable([
            'ExchangeMarketBIC' => $rate->getExchangeMarketBic(),
            'RateDate' => $rate->getRateDate()?->format(DATE_ATOM),
        ]);
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

        $source = $address->toArray();
        $payload = [];

        if ($source['street'] !== null && $source['street'] !== '') {
            $payload['StreetName'] = $source['street'];
        }

        if ($source['buildingNumber'] !== null && $source['buildingNumber'] !== '') {
            $payload['BuildingNumber'] = $source['buildingNumber'];
        }

        if ($source['city'] !== null && $source['city'] !== '') {
            $payload['CityName'] = $source['city'];
        }

        if ($source['postalCode'] !== null && $source['postalCode'] !== '') {
            $payload['PostalZone'] = $source['postalCode'];
        }

        if ($source['countryIso'] !== null && $source['countryIso'] !== '') {
            $payload['CountryIso'] = $source['countryIso'];
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

        foreach ($identifications as $identification) {
            $row = [
                'ID' => $identification->getId(),
                'Scheme' => $identification->getScheme()->value,
            ];

            $result[] = $row + $this->filterNullable([
                'OriginCountry' => $identification->getOriginCountry(),
            ]);
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
            $result[] = [
                'Text' => $description->getText(),
            ] + $this->filterNullable([
                'LangID' => $description->getLangId(),
            ]);
        }

        return $result;
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    private function filterNullable(array $data): array
    {
        return array_filter(
            $data,
            static fn(mixed $value): bool => $value !== null && $value !== '' && $value !== [],
        );
    }
}
