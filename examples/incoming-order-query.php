<?php

declare(strict_types=1);

/**
 * Example: IncomingOrder query workflow
 *
 * Demonstrates:
 * - query request
 * - formatted output
 */

use Lemonade\Vario\ValueObject\IncomingOrderQuery;
use Lemonade\Vario\VarioApi;

/** @var VarioApi $vario */

/*
|--------------------------------------------------------------------------
| Query definition
|--------------------------------------------------------------------------
|
| Prepares IncomingOrder query sent to Vario API.
| Page length is limited to 1 item here so the example output
| stays short and easy to inspect during development.
|
*/

$query = (new IncomingOrderQuery())
    ->withPageLength(1);

echo '<pre>';

/*
|--------------------------------------------------------------------------
| Query request
|--------------------------------------------------------------------------
|
| Loads incoming orders from Vario API and maps raw transport payloads
| into strongly typed IncomingOrder domain objects.
|
*/

echo "\n=== IncomingOrder query ===\n";

$items = $vario->incomingOrders()->query($query);

/*
|--------------------------------------------------------------------------
| Formatted output
|--------------------------------------------------------------------------
|
| Converts mapped domain objects into a readable debug structure.
| This is usually more practical than dumping the whole object graph
| directly, especially when verifying mapper behavior.
|
*/

$formatted = [];

foreach ($items as $order) {
    $formatted[] = [
        'uuid' => $order->getUuid(),
        'id' => $order->getId(),
        'issueDate' => $order->getIssueDate()?->format(DATE_ATOM),
        'currency' => $order->getCurrency()?->value,
        'paymentMeansCode' => $order->getPaymentMeansCode()?->value,

        'buyerCustomerParty' => [
            'name' => $order->getBuyerCustomerParty()?->getName(),
            'email' => $order->getBuyerCustomerParty()?->getEmail(),
            'companyNumber' => $order->getBuyerCustomerParty()?->getCompanyNumber(),
            'vatId' => $order->getBuyerCustomerParty()?->getVatId(),
        ],

        'sellerSupplierParty' => [
            'name' => $order->getSellerSupplierParty()?->getName(),
            'email' => $order->getSellerSupplierParty()?->getEmail(),
            'companyNumber' => $order->getSellerSupplierParty()?->getCompanyNumber(),
            'vatId' => $order->getSellerSupplierParty()?->getVatId(),
        ],

        'deliveryDetail' => [
            'optionCodes' => $order->getDeliveryDetail()?->getOptionCodes(),
            'requestedDeliveryDate' => $order->getDeliveryDetail()?->getRequestedDeliveryDate()?->format(DATE_ATOM),
        ],

        'textualAttributes' => array_map(
            static fn($attribute) => [
                'kind' => $attribute->getAttributeKind()->value,
                'name' => $attribute->getName(),
                'value' => $attribute->getValue(),
                'langId' => $attribute->getLangId(),
            ],
            $order->getTextualAttributes(),
        ),

        'lines' => array_map(
            static fn($line) => [
                'uuid' => $line->getUuid(),
                'id' => $line->getId(),
                'description' => $line->getLineItem()?->getPrimaryDescriptionText(),
                'qty' => $line->getLineQuantity()?->getValue(),
                'unit' => $line->getLineQuantity()?->getUnitCode(),
                'unitPrice' => $line->getUnitPrice()?->getAmount(),
                'unitPriceTaxInclusive' => $line->getUnitPrice()?->getAmountTaxInclusive(),
                'lineTotal' => $line->getLineExtensionAmount(),
                'lineTotalTaxInclusive' => $line->getLineExtensionAmountTaxInclusive(),
            ],
            $order->getDocumentLines(),
        ),
    ];
}

print_r([
    'count' => count($formatted),
    'items' => $formatted,
]);
