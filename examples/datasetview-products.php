<?php

declare(strict_types=1);

/**
 * Example: ProductMapper usage
 *
 * Demonstrates different mapping strategies:
 * - iterate()  → streaming generator
 * - collect()  → materialized ProductCollection
 * - lazy()     → lazy processing pipeline
 */

use Lemonade\Vario\Domain\Product\Mapper\ProductAttributesMapper;
use Lemonade\Vario\Domain\Product\Mapper\ProductClassificationMapper;
use Lemonade\Vario\Domain\Product\Mapper\ProductDescriptionMapper;
use Lemonade\Vario\Domain\Product\Mapper\ProductDimensionsMapper;
use Lemonade\Vario\Domain\Product\Mapper\ProductFlagsMapper;
use Lemonade\Vario\Domain\Product\Mapper\ProductIdentifiersMapper;
use Lemonade\Vario\Domain\Product\Mapper\ProductIdentityMapper;
use Lemonade\Vario\Domain\Product\Mapper\ProductInventoryMapper;
use Lemonade\Vario\Domain\Product\Mapper\ProductPricesMapper;
use Lemonade\Vario\Domain\Product\Mapper\ProductPricingMapper;
use Lemonade\Vario\Domain\Product\Mapping\ProductAttributesMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductClassificationMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductDescriptionMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductDimensionsMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductFlagsMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductIdentifiersMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductIdentityMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductInventoryMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductPricesMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductPricingMapping;
use Lemonade\Vario\Domain\Product\Product;
use Lemonade\Vario\Domain\Product\ProductDatasetMapping;
use Lemonade\Vario\Domain\Product\ProductMapper;
use Lemonade\Vario\ValueObject\CustomDatasetView;
use Lemonade\Vario\ValueObject\DatasetViewQuery;
use Lemonade\Vario\VarioApi;

/** @var VarioApi $vario */

$productQuery = DatasetViewQuery::for(
    view: new CustomDatasetView('Katalog/KatalogCenikAPI'),
    pageLength: 1
);

$result = $vario->datasetView()->fetch($productQuery);

$productArray = $result->getRows();

/*
|--------------------------------------------------------------------------
| Mapper configuration
|--------------------------------------------------------------------------
*/
$mapping = new ProductDatasetMapping();

$mapping
    ->add(new ProductIdentityMapper(new ProductIdentityMapping()))
    ->add(new ProductIdentifiersMapper(new ProductIdentifiersMapping()))
    ->add(new ProductDescriptionMapper(new ProductDescriptionMapping()))
    ->add(new ProductFlagsMapper(new ProductFlagsMapping()))
    ->add(new ProductDimensionsMapper(new ProductDimensionsMapping()))
    ->add(new ProductPricingMapper(new ProductPricingMapping()))
    ->add(new ProductPricesMapper(new ProductPricesMapping()))
    ->add(new ProductInventoryMapper(new ProductInventoryMapping()))
    ->add(new ProductClassificationMapper(new ProductClassificationMapping()))
    ->add(new ProductAttributesMapper(new ProductAttributesMapping()));

$mapper = new ProductMapper($mapping);

echo '<pre>';

/*
|--------------------------------------------------------------------------
| DatasetView metadata
|--------------------------------------------------------------------------
|
| The fetch() method returns a typed DatasetViewResult object.
|
| It provides convenient access to pagination metadata returned
| by the Vario API such as total record count, number of pages
| and the current page index.
|
| This information can be useful when debugging queries,
| building synchronization jobs or implementing progress
| indicators for large dataset imports.
|
*/

echo "\n=== DatasetView metadata ===\n";

print_r([
    'records' => $result->getRecordCount(),
    'pages' => $result->getPageCount(),
    'pageIndex' => $result->getPageIndex(),
    'pageLength' => $result->getPageLength(),
]);

/*
|--------------------------------------------------------------------------
| Streaming (array → mapper)
|--------------------------------------------------------------------------
|
| Maps an array of DatasetView rows into Product domain objects.
|
| ProductMapper::iterate() uses a generator internally, meaning that
| only one Product instance exists in memory at a time during iteration.
|
| This approach is efficient when the dataset has already been fetched
| using DatasetViewApi::fetch() or DatasetViewApi::get().
|
*/
echo "\n=== Streaming (iterate) ===\n";

foreach ($mapper->iterate($productArray) as $product) {

    $identity = $product->identity();
    $pricing = $product->pricing();
    $inventory = $product->inventory();

    $price = $pricing?->getPrice();

    // Structured pricing (multiple price levels)
    $prices = $product->prices();

    $levels = [];

    if ($prices !== null) {
        foreach ($prices->getLevels() as $code => $level) {
            $levels[$code] = [
                'value' => $level->getPrice()->getValue(),
                'vat' => $level->getPrice()->getVatPercentage(),
                'currency' => $level->getPrice()->getCurrency()?->value,
            ];
        }
    }

    print_r([
        'identity' => [
            'uuid' => $identity?->getUuid(),
            'sku' => $identity?->getSku(),
            'name' => $identity?->getName(),
        ],

        // Simple pricing
        'simplePrice' => [
            'value' => $price?->getValue(),
            'vatRate' => $price?->getVatPercentage(),
            'currency' => $price?->getCurrency()?->value,
            'gross' => $price?->isGross(),
        ],

        // Structured pricing
        'structuredPricing' => [
            'basePrice' => $prices?->getBasePrice()?->getValue(),
            'levels' => $levels,
        ],

        // Inventory example
        'inventory' => [
            'stock' => $inventory?->getStock(),
            'deliveryTime' => $inventory?->getDeliveryTime(),
        ],
    ]);
}

/*
|--------------------------------------------------------------------------
| Streaming (API → mapper)
|--------------------------------------------------------------------------
|
| Demonstrates full streaming directly from the Vario API.
|
| DatasetViewApi::iterate() loads DatasetView pages lazily,
| while ProductMapper::iterate() converts each row into
| a Product domain object.
|
| This allows processing very large catalogues without
| loading the entire dataset into memory.
|
*/
echo "\n=== Streaming (API → Mapper) ===\n";

foreach ($mapper->iterate($vario->datasetView()->iterate($productQuery)) as $product) {

    $identity = $product->identity();
    $pricing = $product->pricing();
    $inventory = $product->inventory();

    $price = $pricing?->getPrice();
    $prices = $product->prices();

    $levels = [];

    if ($prices !== null) {
        foreach ($prices->getLevels() as $code => $level) {
            $levels[$code] = [
                'value' => $level->getPrice()->getValue(),
                'vat' => $level->getPrice()->getVatPercentage(),
                'currency' => $level->getPrice()->getCurrency()?->value,
            ];
        }
    }

    print_r([
        'identity' => [
            'uuid' => $identity?->getUuid(),
            'sku' => $identity?->getSku(),
            'name' => $identity?->getName(),
        ],
        'simplePrice' => [
            'value' => $price?->getValue(),
            'vatRate' => $price?->getVatPercentage(),
            'currency' => $price?->getCurrency()?->value,
            'gross' => $price?->isGross(),
        ],
        'structuredPricing' => [
            'basePrice' => $prices?->getBasePrice()?->getValue(),
            'levels' => $levels,
        ],
        'inventory' => [
            'stock' => $inventory?->getStock(),
            'deliveryTime' => $inventory?->getDeliveryTime(),
        ],
    ]);
}

/*
|---------------------------------------------------------------------------
| Materialized collection (collect)
|---------------------------------------------------------------------------
|
| Converts all mapped products into a ProductCollection.
|
| This loads the entire dataset into memory but provides
| convenient collection helpers such as:
|
|   - count()
|   - first()
|   - iteration
|
| Useful when working with smaller datasets where full
| in-memory access is required.
|
*/
echo "\n=== Materialized collection (collect) ===\n";

$products = $mapper->collect($productArray);
print_r([
    'count' => $products->count(),
    'first' => $products->first()?->identity()?->toArray(),
]);

/*
|---------------------------------------------------------------------------
| Lazy pipeline (lazy)
|---------------------------------------------------------------------------
|
| Creates a lazy processing pipeline over the mapped products.
|
| Operations such as filter() are applied during iteration,
| meaning the full dataset is never loaded into memory.
|
| This allows efficient processing of large datasets
| while still providing expressive filtering logic.
|
*/
echo "\n=== Lazy pipeline (lazy) ===\n";

$products = $mapper
    ->lazy($productArray)
    ->filter(
        fn(Product $p) => $p->identity()?->getUuid() === 'e32a9553-1a2d-4bfd-a32f-000e2e600261'
    );

foreach ($products as $product) {
    $price = $product->pricing()?->getPrice();

    print_r([
        'name' => $product->identity()?->getName(),
        'stock' => $product->inventory()?->getStock(),
        'price' => $price?->getValue(),
        'vatRate' => $price?->getVatPercentage(),
    ]);
}

/*
|---------------------------------------------------------------------------
| Lazy → Collection
|---------------------------------------------------------------------------
|
| Executes the lazy pipeline and materializes the filtered result
| into a ProductCollection.
|
| This pattern is useful when you want to:
|
|   1) filter large datasets efficiently
|   2) then work with the final result in memory
|
*/
echo "\n=== Lazy → Collection ===\n";

$products = $mapper
    ->lazy($productArray)
    ->filter(fn(Product $p) => $p->inventory()?->getStock() > 10)
    ->collect();

print_r([
    'count' => $products->count(),
    'first' => $products->first()?->identity()?->toArray(),
]);

/*
|---------------------------------------------------------------------------
| Full mapped product example
|---------------------------------------------------------------------------
|
| Demonstrates how to access all ProductSection objects
| from the Product aggregate.
|
| Each section represents a normalized domain view
| of the DatasetView row (identity, pricing, inventory, etc.).
|
| This example converts the entire Product object graph
| into a serializable array structure.
|
*/
echo "\n=== Full mapped products ===\n";

$formattedProducts = [];

foreach ($products as $product) {

    $formattedProducts[] = [
        'identity' => $product->identity()?->toArray(),
        'description' => $product->description()?->toArray(),
        'flags' => $product->flags()?->toArray(),
        'dimensions' => $product->dimensions()?->toArray(),
        'pricing' => $product->pricing()?->toArray(),
        'inventory' => $product->inventory()?->toArray(),
        'identifiers' => $product->identifiers()?->toArray(),
        'classification' => $product->classification()?->toArray(),
        'attributes' => $product->attributes()?->toArray(),
    ];
}

print_r([
    'count' => $products->count(),
    'items' => $formattedProducts,
]);
