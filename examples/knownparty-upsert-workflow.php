<?php

declare(strict_types=1);

/**
 * Example: KnownParty upsert workflow
 *
 * Demonstrates:
 * - payload preview
 * - upsert request
 * - reading the created entity
 */

use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\KnownParty\KnownPartyKind;
use Lemonade\Vario\Domain\Shared\Identification;
use Lemonade\Vario\Domain\Shared\IdentificationScheme;
use Lemonade\Vario\Domain\Shared\PostalAddress;
use Lemonade\Vario\Query\QueryFilters;
use Lemonade\Vario\ValueObject\KnownPartyQuery;
use Lemonade\Vario\VarioApi;

/** @var VarioApi $vario */

$input = (new KnownPartyInput('Example Company s.r.o.'))
    ->withKind(KnownPartyKind::Organization)
    ->withUuid('11111111-2222-3333-4444-555555555555')
    ->withContactPerson('Example Contact')
    ->withEmail('contact@example.com')
    ->withTelephone('+420123456789')
    ->withAddress(new PostalAddress(
        street: 'Example Street',
        buildingNumber: '10',
        city: 'Prague',
        postalCode: '11000',
        countryIso: 'CZ'
    ))
    ->addIdentification(new Identification(
        scheme: IdentificationScheme::UIN,
        id: '12345678',
        originCountry: 'CZ'
    ));

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

$preview = $vario->knownParties()->previewUpsert([
    $input,
]);

print_r($preview);


/*
|--------------------------------------------------------------------------
| Upsert request
|--------------------------------------------------------------------------
|
| Sends the PUT request to Vario API and returns confirmation objects.
|
*/

echo "\n=== Upsert result ===\n";

$result = $vario->knownParties()->upsert([
    $input,
]);

print_r(array_map(
    fn($r) => $r->toArray(),
    $result
));


/*
|--------------------------------------------------------------------------
| Query verification
|--------------------------------------------------------------------------
|
| Reads the entity again from API to confirm stored data.
|
*/

echo "\n=== Query verification ===\n";

$query = (new KnownPartyQuery())
    ->withFilter(
        QueryFilters::equals('ElectronicMail', 'contact@example.com')
    );

$items = $vario->knownParties()->query($query);

$formatted = [];

foreach ($items as $party) {

    $formatted[] = [
        'uuid' => $party->getUuid(),
        'name' => $party->getName(),
        'email' => $party->getEmail(),
        'telephone' => $party->getTelephone(),
        'address' => $party->getPostalAddress()?->toArray(),
        'companyNumber' => $party->getCompanyNumber(),
        'vatId' => $party->getVatId(),
    ];
}

print_r([
    'count' => count($formatted),
    'items' => $formatted,
]);
