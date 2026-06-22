<?php

declare(strict_types=1);

/**
 * Example: OutgoingQuotation upsert using strict DTOs
 *
 * Demonstrates:
 * - buyer setup
 * - seller setup
 * - identifications
 * - quotation line setup
 * - monetary total
 * - tax exchange rate
 * - tax total
 * - payload preview
 * - real upsert call (commented out)
 */

use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Enum\OutgoingQuotationPaymentMeansCode;
use Lemonade\Vario\Domain\OutgoingQuotation\Enum\OutgoingQuotationTaxCalculationMethod;
use Lemonade\Vario\Domain\OutgoingQuotation\Enum\OutgoingQuotationTaxScheme;
use Lemonade\Vario\Domain\OutgoingQuotation\Enum\OutgoingQuotationUnitOfMeasureScheme;
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
use Lemonade\Vario\VarioApi;

/** @var VarioApi $vario */

/*
|--------------------------------------------------------------------------
| Buyer
|--------------------------------------------------------------------------
*/
$buyer = (new KnownPartyInput('A - Storex, v.o.s.'))
    ->addIdentification(new Identification(
        scheme: IdentificationScheme::UIN,
        id: '620927153',
        originCountry: 'CZ'
    ));

/*
|--------------------------------------------------------------------------
| Seller
|--------------------------------------------------------------------------
|
| The Vario payload contains only the VAT identification.
| KnownPartyInput requires a constructor name, so an empty string is used.
|
*/
$seller = (new KnownPartyInput(''))
    ->addIdentification(new Identification(
        scheme: IdentificationScheme::VAT,
        id: 'CZ61681229',
        originCountry: 'CZ'
    ));

/*
|--------------------------------------------------------------------------
| Quotation line
|--------------------------------------------------------------------------
*/
$lineItem = (new OutgoingQuotationLineItemInput())
    ->withCatalogueItemIdentification('A25882')
    ->addDescription(
        new OutgoingQuotationDescription('Adam křeslo - skládačka')
    );

$line = new OutgoingQuotationLineInput(
    uuid: 'd4b5b29c-d658-4568-aaa9-839f11ce1446',
    lineExtensionAmount: 95.0,
    lineExtensionAmountTaxInclusive: 114.95,
    lineItem: $lineItem,
    lineQuantity: new OutgoingQuotationQuantity(
        value: 1.0,
        unitCode: 'Ks',
        scheme: OutgoingQuotationUnitOfMeasureScheme::Unknown
    ),
    taxSubTotal: new OutgoingQuotationTaxSubTotal(
        calculationMethod: OutgoingQuotationTaxCalculationMethod::Add,
        scheme: OutgoingQuotationTaxScheme::Vat,
        taxableAmount: 95.0,
        taxAmount: 19.95,
        taxPercentage: 21.0
    ),
    id: '1'
);

/*
|--------------------------------------------------------------------------
| Monetary total, tax exchange rate, tax total
|--------------------------------------------------------------------------
*/
$quotation = new OutgoingQuotationInput(
    uuid: 'c676048c-3789-4228-82b2-9ca6e7b952f7',
    issueDate: new DateTimeImmutable('2026-06-18T00:00:00+02:00'),
    currency: Currency::CZK,
    buyerCustomerParty: $buyer,
    sellerSupplierParty: $seller,
    monetaryTotal: new OutgoingQuotationMonetaryTotal(
        payableAmount: 115.0,
        payableRoundingAmount: 0.05,
        taxExclusiveAmount: 95.0,
        taxInclusiveAmount: 114.95
    ),
    taxExchangeRate: new OutgoingQuotationTaxExchangeRate(
        taxCurrency: Currency::CZK,
        referenceCurrencyRate: 1.0,
        taxCurrencyRate: 1.0
    ),
    taxTotal: new OutgoingQuotationTaxTotal(
        taxAmount: 19.95,
        taxSubTotals: [
            new OutgoingQuotationTaxSubTotal(
                calculationMethod: OutgoingQuotationTaxCalculationMethod::Total,
                scheme: OutgoingQuotationTaxScheme::Vat,
                taxableAmount: 95.0,
                taxAmount: 19.95,
                taxPercentage: 21.0
            ),
        ]
    ),
    documentLines: [
        $line,
    ],
    id: 'ZAKTEST-2026-00002',
    paymentMeansCode: OutgoingQuotationPaymentMeansCode::Cash
);

echo '<pre>';

/*
|--------------------------------------------------------------------------
| Preview payload
|--------------------------------------------------------------------------
*/
echo "\n=== Preview payload ===\n";

$preview = $vario->outgoingQuotations()->previewUpsert([
    $quotation,
]);

print_r($preview);

/*
|--------------------------------------------------------------------------
| Upsert request
|--------------------------------------------------------------------------
*/
// echo "\n=== Upsert result ===\n";
//
// $result = $vario->outgoingQuotations()->upsert([
//     $quotation,
// ]);
//
// print_r(array_map(
//     fn($row) => $row->toArray(),
//     $result
// ));
