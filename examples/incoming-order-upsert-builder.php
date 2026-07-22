<?php

declare(strict_types=1);

/**
 * Example: IncomingOrder upsert builder workflow
 *
 * Demonstrates:
 * - high-level line definition
 * - automatic line calculation
 * - automatic total calculation
 * - payload preview
 * - upsert request
 */

use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\IncomingOrder\Builder\IncomingOrderBuildInput;
use Lemonade\Vario\Domain\IncomingOrder\Builder\IncomingOrderBuilder;
use Lemonade\Vario\Domain\IncomingOrder\Builder\IncomingOrderBuildPartiesInput;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPaymentMeansCode;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineItemInput;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentPriceMode;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTextualAttributeKind;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentDescription;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentUnitConversionFactor;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentBuildIdentityInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentAdditionalAttributeInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentAdditionalAttributeValueInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineIdentityInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLinePriceInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineQuantityInput;
use Lemonade\Vario\VarioApi;
use Lemonade\Vario\Domain\Shared\Identification;
use Lemonade\Vario\Domain\Shared\IdentificationScheme;

/** @var VarioApi $vario */

$buyer = (new KnownPartyInput('A - Storex, v.o.s.'))
    ->addIdentification(new Identification(
        scheme: IdentificationScheme::UIN,
        id: '620927153',
        originCountry: 'CZ',
    ));

$accounting = $buyer;
$delivery = $buyer;

$seller = (new KnownPartyInput(''))
    ->addIdentification(new Identification(
        scheme: IdentificationScheme::VAT,
        id: 'CZ61681229',
        originCountry: 'CZ',
    ));

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

$builder = new IncomingOrderBuilder();

$lines = [
    new DocumentCalculatedLineInput(
        identity: new DocumentCalculatedLineIdentityInput(
            uuid: 'd2045e34-49b4-4238-84e2-950362f2007e',
        ),
        lineItem: $lineItem1,
        quantity: new DocumentCalculatedLineQuantityInput(
            value: 1.0,
            unitCode: 'Ks',
        ),
        price: new DocumentCalculatedLinePriceInput(
            unitPrice: 1500.0,
            vatRate: 21.0,
            priceMode: DocumentPriceMode::WithoutVat,
            lineAllowanceAmount: 300.0,
        ),
        // unitPrice is the final unit price without VAT after discount.
        // lineAllowanceAmount is the discount without VAT.
        // In Vario, base price before discount is derived as LineExtensionAmount + LineAllowanceAmount.
        // LineAllowanceAmount does not change SDK totals.
    ),
    new DocumentCalculatedLineInput(
        identity: new DocumentCalculatedLineIdentityInput(
            uuid: '935f3ea7-3fda-40d8-af8f-a5582fc81f54',
        ),
        lineItem: $lineItem2,
        quantity: new DocumentCalculatedLineQuantityInput(
            value: 1.0,
            unitCode: 'Ks',
        ),
        price: new DocumentCalculatedLinePriceInput(
            unitPrice: 300.0,
            vatRate: 21.0,
            priceMode: DocumentPriceMode::WithoutVat,
        ),
    ),
];

$order = $builder->build(new IncomingOrderBuildInput(
    identity: new DocumentBuildIdentityInput(
        uuid: 'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
        id: 'eshop0001',
    ),
    issueDate: new DateTimeImmutable('2024-04-02T00:00:00+02:00'),
    currency: Currency::CZK,
    parties: new IncomingOrderBuildPartiesInput(
        buyerCustomerParty: $buyer,
        sellerSupplierParty: $seller,
        accountingCustomerParty: $accounting,
        delivery: $delivery,
    ),
    lines: $lines,
    partialDeliveryIndicator: false,
    paymentMeansCode: IncomingOrderPaymentMeansCode::Cheque,
));

echo '<pre>';
echo "\n=== Preview payload ===\n";

$preview = $vario->incomingOrders()->previewUpsert([
    $order,
]);

print_r($preview);
