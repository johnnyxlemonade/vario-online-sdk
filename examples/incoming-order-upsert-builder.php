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
use Lemonade\Vario\Domain\IncomingOrder\Builder\IncomingOrderBuilder;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPaymentMeansCode;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPriceMode;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTextualAttributeKind;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderUnitOfMeasureScheme;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderDescription;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderUnitConversionFactor;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderAdditionalAttributeInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderCalculatedLineInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineItemInput;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\Shared\Identification;
use Lemonade\Vario\Domain\Shared\IdentificationScheme;
use Lemonade\Vario\Domain\Shared\PostalAddress;
use Lemonade\Vario\VarioApi;

/** @var VarioApi $vario */

$buyer = (new KnownPartyInput('1. česká podvodná'))
    ->withContactPerson('Rybana Wassermannová')
    ->withEmail('pod.vodnik@zaby.cz')
    ->withTelephone('+420557788996')
    ->withAddress(new PostalAddress(
        street: 'Vodná',
        buildingNumber: '57',
        city: 'Žabovřesky',
        postalCode: '566 00',
        countryIso: null
    ))
    ->addIdentification(new Identification(
        scheme: IdentificationScheme::UIN,
        id: '89745612',
        originCountry: 'CZ'
    ))
    ->addIdentification(new Identification(
        scheme: IdentificationScheme::VAT,
        id: 'CZ89745612',
        originCountry: 'CZ'
    ));

$accounting = (new KnownPartyInput('1. česká podvodná'))
    ->withContactPerson('Rybana Wassermannová')
    ->withEmail('pod.vodnik@zaby.cz')
    ->withTelephone('+420557788996')
    ->withAddress(new PostalAddress(
        street: 'Vodná',
        buildingNumber: '57',
        city: 'Žabovřesky',
        postalCode: '566 00',
        countryIso: null
    ))
    ->addIdentification(new Identification(
        scheme: IdentificationScheme::UIN,
        id: '89745612',
        originCountry: 'CZ'
    ))
    ->addIdentification(new Identification(
        scheme: IdentificationScheme::VAT,
        id: 'CZ89745612',
        originCountry: 'CZ'
    ));

$delivery = (new KnownPartyInput('1. česká podvodná'))
    ->withContactPerson('Vodomil Wassermann')
    ->withEmail('pod.vodnik@zaby.cz')
    ->withTelephone('+420557788996')
    ->withAddress(new PostalAddress(
        street: 'Vodná',
        buildingNumber: '57',
        city: 'Žabovřesky',
        postalCode: '566 00',
        countryIso: null
    ))
    ->addIdentification(new Identification(
        scheme: IdentificationScheme::UIN,
        id: '89745612',
        originCountry: 'CZ'
    ))
    ->addIdentification(new Identification(
        scheme: IdentificationScheme::VAT,
        id: 'CZ89745612',
        originCountry: 'CZ'
    ));

/*
|--------------------------------------------------------------------------
| Seller
|--------------------------------------------------------------------------
|
| The example payload contains only the VAT ID.
| However, KnownPartyInput requires a name in the constructor,
| so an empty string is used here intentionally.
|
*/
$seller = (new KnownPartyInput(''))
    ->addIdentification(new Identification(
        scheme: IdentificationScheme::VAT,
        id: 'CZ11223344',
        originCountry: 'CZ'
    ));

/*
|--------------------------------------------------------------------------
| Line item definitions
|--------------------------------------------------------------------------
|
| These objects still define item identity and descriptive metadata.
| The difference is that numeric totals are no longer prepared manually.
|
*/
$lineItem1 = (new IncomingOrderLineItemInput())
    ->withCatalogueItemIdentification('grosh big')
    ->withSellersItemIdentification('děravý groš')
    ->addDescription(
        new IncomingOrderDescription('děravý groš')
    )
    ->addAdditionalAttribute(
        new IncomingOrderAdditionalAttributeInput(
            name: 'Varianta',
            value: 'velká díra',
            attributeKind: IncomingOrderTextualAttributeKind::ExtendedID,
            langId: null,
            unitCode: null,
            scheme: IncomingOrderUnitOfMeasureScheme::Unknown
        )
    )
    ->addUnitConversionFactor(
        new IncomingOrderUnitConversionFactor(
            value: 1.0,
            unitCode: 'Ks',
            scheme: IncomingOrderUnitOfMeasureScheme::Unknown
        )
    );

$lineItem2 = (new IncomingOrderLineItemInput())
    ->withCatalogueItemIdentification('BELA')
    ->withSellersItemIdentification('stará bela')
    ->addDescription(
        new IncomingOrderDescription('stará bela')
    )
    ->addUnitConversionFactor(
        new IncomingOrderUnitConversionFactor(
            value: 1.0,
            unitCode: 'Ks',
            scheme: IncomingOrderUnitOfMeasureScheme::Unknown
        )
    )
    ->addUnitConversionFactor(
        new IncomingOrderUnitConversionFactor(
            value: 2.0,
            unitCode: 'm2',
            scheme: IncomingOrderUnitOfMeasureScheme::SI
        )
    );

/*
|--------------------------------------------------------------------------
| Builder
|--------------------------------------------------------------------------
|
| The builder accepts simplified line definitions and calculates:
|
| - line totals without VAT
| - line totals with VAT
| - line tax subtotals
| - document monetary total
| - document tax total
|
*/
$builder = new IncomingOrderBuilder();

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
    new IncomingOrderCalculatedLineInput(
        uuid: 'd2045e34-49b4-4238-84e2-950362f2007e',
        lineItem: $lineItem1,
        quantity: 1.0,
        unitCode: 'Ks',
        unitPrice: 1500.0,
        vatRate: 21.0,
        priceMode: IncomingOrderPriceMode::WithoutVat,
    ),
    new IncomingOrderCalculatedLineInput(
        uuid: '935f3ea7-3fda-40d8-af8f-a5582fc81f54',
        lineItem: $lineItem2,
        quantity: 1.0,
        unitCode: 'Ks',
        unitPrice: 300.0,
        vatRate: 21.0,
        priceMode: IncomingOrderPriceMode::WithoutVat,
    ),
];

/*
|--------------------------------------------------------------------------
| Build order
|--------------------------------------------------------------------------
|
| Produces a complete low-level IncomingOrderInput instance
| compatible with previewUpsert() and upsert().
|
| TaxExchangeRate is omitted here intentionally.
| The builder will create a default 1:1 exchange rate for the order currency.
|
*/
$order = $builder->build(
    uuid: 'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
    issueDate: new DateTimeImmutable('2024-04-02T00:00:00+02:00'),
    currency: Currency::CZK,
    buyerCustomerParty: $buyer,
    sellerSupplierParty: $seller,
    lines: $lines,
    id: 'eshop0001',
    accountingCustomerParty: $accounting,
    delivery: $delivery,
    note: null,
    partialDeliveryIndicator: false,
    paymentMeansCode: IncomingOrderPaymentMeansCode::Cheque,
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

$preview = $vario->incomingOrders()->previewUpsert([
    $order,
]);

print_r($preview);

/*
|--------------------------------------------------------------------------
| Upsert request
|--------------------------------------------------------------------------
|
| Sends the PUT request to the Vario API
| and returns confirmation objects.
|
*/

// echo "\n=== Upsert result ===\n";
//
// $result = $vario->incomingOrders()->upsert([
//     $order,
// ]);
//
// print_r(array_map(
//     fn($r) => $r->toArray(),
//     $result
// ));
