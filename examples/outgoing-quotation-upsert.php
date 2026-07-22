<?php

declare(strict_types=1);

/**
 * Example: OutgoingQuotation upsert builder workflow
 *
 * Demonstrates:
 * - buyer setup
 * - seller setup
 * - identifications
 * - high-level line definition
 * - automatic line calculation
 * - automatic monetary total and tax total calculation
 * - payload preview
 * - upsert request
 */

use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Builder\OutgoingQuotationBuilder;
use Lemonade\Vario\Domain\OutgoingQuotation\Enum\OutgoingQuotationPaymentMeansCode;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationLineItemInput;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentPriceMode;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentDescription;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineInput;
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
        originCountry: 'CZ',
    ));

/*
|--------------------------------------------------------------------------
| Seller
|--------------------------------------------------------------------------
|
| The example payload contains only the VAT ID.
| KnownPartyInput requires a name in the constructor,
| so an empty string is used here intentionally.
| The normalizer omits empty names from the final payload.
|
*/
$seller = (new KnownPartyInput(''))
    ->addIdentification(new Identification(
        scheme: IdentificationScheme::VAT,
        id: 'CZ61681229',
        originCountry: 'CZ',
    ));

/*
|--------------------------------------------------------------------------
| Line item definition
|--------------------------------------------------------------------------
|
| This object defines item identity and descriptive metadata.
| Numeric totals are calculated by the builder from the high-level line input.
|
*/
$lineItem = (new OutgoingQuotationLineItemInput())
    ->withCatalogueItemIdentification('A25882')
    ->addDescription(
        new DocumentDescription('Adam kreslo - skladacka')
    );

/*
|--------------------------------------------------------------------------
| Builder
|--------------------------------------------------------------------------
|
| The builder accepts simplified line definitions and calculates:
|
| - line total without VAT
| - line total with VAT
| - line tax subtotal
| - document monetary total
| - document tax total
|
*/
$builder = new OutgoingQuotationBuilder();

/*
|--------------------------------------------------------------------------
| High-level line definitions
|--------------------------------------------------------------------------
|
| Only quantity, unit price, VAT rate and VAT mode are provided here.
| The builder calculates all low-level numeric structures automatically.
|
*/
$lines = [
    new DocumentCalculatedLineInput(
        uuid: 'd4b5b29c-d658-4568-aaa9-839f11ce1446',
        lineItem: $lineItem,
        quantity: 1.0,
        unitCode: 'Ks',
        unitPrice: 95.0,
        vatRate: 21.0,
        priceMode: DocumentPriceMode::WithoutVat,
        // Discount without VAT, not the base price. Base price before discount = unitPrice * quantity + lineAllowanceAmount = 95 + 25.
        lineAllowanceAmount: 25.0,
        id: '1',
    ),
];

/*
|--------------------------------------------------------------------------
| Build quotation
|--------------------------------------------------------------------------
|
| Produces a complete low-level OutgoingQuotationInput instance
| compatible with previewUpsert() and upsert().
|
| TaxExchangeRate is omitted here intentionally.
| The builder will create a default 1:1 exchange rate for the quotation currency.
|
| PayableRoundingAmount is set explicitly to match the Vario sample payload.
|
*/
$quotation = $builder->build(
    uuid: 'c676048c-3789-4228-82b2-9ca6e7b952f7',
    issueDate: new DateTimeImmutable('2026-06-18T00:00:00+02:00'),
    currency: Currency::CZK,
    buyerCustomerParty: $buyer,
    sellerSupplierParty: $seller,
    lines: $lines,
    id: 'ZAKTEST-2026-00002',
    paymentMeansCode: OutgoingQuotationPaymentMeansCode::Cash,
    payableRoundingAmount: 0.05,
);

echo '<pre>';

/*
|--------------------------------------------------------------------------
| Preview payload
|--------------------------------------------------------------------------
|
| Shows the exact request payload that will be sent to the API.
| Useful for debugging integrations before performing real requests.
|
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
|
| Sends the PUT request to the Vario API.
| This requires access to the target Vario server, for example through VPN.
|
*/

// echo "\n=== Upsert result ===\n";
//
// $result = $vario->outgoingQuotations()->upsert([
//     $quotation,
// ]);
//
// print_r(array_map(
//     static fn($row): array => $row->toArray(),
//     $result
// ));
