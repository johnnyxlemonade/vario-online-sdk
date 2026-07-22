<?php

declare(strict_types=1);

/**
 * Example: OutgoingQuotation upsert builder workflow
 */

use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Builder\OutgoingQuotationBuilder;
use Lemonade\Vario\Domain\OutgoingQuotation\Builder\OutgoingQuotationBuildInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Enum\OutgoingQuotationPaymentMeansCode;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationLineItemInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationPartiesInput;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentPriceMode;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentDescription;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineIdentityInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLinePriceInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineQuantityInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentIdentityInput;
use Lemonade\Vario\Domain\Shared\Identification;
use Lemonade\Vario\Domain\Shared\IdentificationScheme;
use Lemonade\Vario\VarioApi;

/** @var VarioApi $vario */

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
    ->addDescription(new DocumentDescription('Adam kreslo - skladacka'));

$builder = new OutgoingQuotationBuilder();

$lines = [
    new DocumentCalculatedLineInput(
        identity: new DocumentCalculatedLineIdentityInput(
            uuid: 'd4b5b29c-d658-4568-aaa9-839f11ce1446',
            id: '1',
        ),
        lineItem: $lineItem,
        quantity: new DocumentCalculatedLineQuantityInput(
            value: 1.0,
            unitCode: 'Ks',
        ),
        price: new DocumentCalculatedLinePriceInput(
            unitPrice: 95.0,
            vatRate: 21.0,
            priceMode: DocumentPriceMode::WithoutVat,
            lineAllowanceAmount: 25.0,
        ),
        // unitPrice is the final unit price without VAT after discount.
        // lineAllowanceAmount is the discount without VAT.
        // In Vario, base price before discount is derived as LineExtensionAmount + LineAllowanceAmount.
        // LineAllowanceAmount does not change SDK totals.
    ),
];

$quotation = $builder->build(new OutgoingQuotationBuildInput(
    identity: new DocumentIdentityInput(
        uuid: 'c676048c-3789-4228-82b2-9ca6e7b952f7',
        id: 'ZAKTEST-2026-00002',
    ),
    issueDate: new DateTimeImmutable('2026-06-18T00:00:00+02:00'),
    currency: Currency::CZK,
    parties: new OutgoingQuotationPartiesInput(
        buyerCustomerParty: $buyer,
        sellerSupplierParty: $seller,
    ),
    lines: $lines,
    paymentMeansCode: OutgoingQuotationPaymentMeansCode::Cash,
    payableRoundingAmount: 0.05,
));

echo '<pre>';
echo "\n=== Preview payload ===\n";

$preview = $vario->outgoingQuotations()->previewUpsert([
    $quotation,
]);

print_r($preview);
