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

$response = $vario->datasetView()->get($productQuery);


$productArray = $response['Data'] ?? [];

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
| Streaming (iterate)
|--------------------------------------------------------------------------
|
| Products are mapped lazily using a generator.
| Only one product exists in memory at a time.
|
| This is the most memory-efficient approach and is recommended
| when processing very large catalogues or API responses.
|
*/
echo "\n=== Streaming (iterate) ===\n";

foreach ($mapper->iterate($productArray) as $product) {

    $price = $product->pricing()?->getPrice();

    print_r([
        'name' => $product->identity()?->getName(),
        'price' => $price?->getValue(),
        'vatRate' => $price?->getVatPercentage(),
        'currency' => $price?->getCurrency()?->value,
    ]);
}

/*
|--------------------------------------------------------------------------
| Materialized collection (collect)
|--------------------------------------------------------------------------
|
| All products are mapped into a ProductCollection instance.
|
| This loads the entire dataset into memory but provides
| convenient collection operations like count(), first(),
| filter(), map() and iteration.
|
*/
echo "\n=== Materialized collection (collect) ===\n";

$products = $mapper->collect($productArray);

print_r([
    'count' => $products->count(),
    'first' => $products->first()?->identity()?->toArray(),
]);

/*
|--------------------------------------------------------------------------
| Lazy pipeline (lazy)
|--------------------------------------------------------------------------
|
| Creates a lazy processing pipeline over the mapped products.
|
| Operations like filter() are applied on-the-fly while iterating,
| so the full dataset is never loaded into memory.
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
|--------------------------------------------------------------------------
| Lazy → Collection
|--------------------------------------------------------------------------
|
| Executes the lazy pipeline and materializes the result
| into a ProductCollection.
|
| Useful when you want memory-efficient filtering first
| and then work with a concrete collection.
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
|--------------------------------------------------------------------------
| Full mapped product example
|--------------------------------------------------------------------------
|
| Example showing how to access all available ProductSection
| objects from the Product aggregate.
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
