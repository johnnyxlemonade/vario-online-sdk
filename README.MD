# Vario Online PHP SDK

[![PHP Version](https://img.shields.io/packagist/php-v/lemonade/vario-online-sdk)](https://packagist.org/packages/lemonade/vario-online-sdk)
[![Packagist Version](https://img.shields.io/packagist/v/lemonade/vario-online-sdk)](https://packagist.org/packages/lemonade/vario-online-sdk)
[![Downloads](https://img.shields.io/packagist/dt/lemonade/vario-online-sdk)](https://packagist.org/packages/lemonade/vario-online-sdk)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-level%20max-brightgreen.svg)](https://phpstan.org/)
[![Tests](https://github.com/johnnyxlemonade/vario-online-sdk/actions/workflows/phpunit.yml/badge.svg)](https://github.com/johnnyxlemonade/vario-online-sdk/actions/workflows/phpunit.yml)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Typed, PSR-compatible PHP SDK for integrating with **Vario Online API**.

This library provides a modern, strongly‑typed integration layer for
systems using **Vario ERP**.

More information about Vario ERP:

https://vario.cz/

The SDK exposes a stable **domain-oriented API**, not just a transport
wrapper. It provides clean abstractions over Vario endpoints, dataset
views and paged queries, including automatic authentication, retry
handling and a fully replaceable HTTP layer.

------------------------------------------------------------------------

## Why this SDK exists

The official Vario Online API exposes data primarily through
generic **DatasetView responses**, which return loosely structured
arrays tied to backend column names.

While flexible, this makes integrations harder to maintain because:

- dataset column names may vary between installations
- responses require manual array parsing
- business logic becomes tightly coupled to API payload structure

This SDK introduces a **domain-oriented layer** on top of the API.

Instead of working with raw arrays, developers interact with
**typed domain models** such as `KnownParty` or `Product`, backed by
immutable value objects and a configurable mapping layer.

The goal is to provide:

- a **stable integration surface**
- strong typing and IDE support
- separation between API payloads and application logic
- a maintainable foundation for ERP integrations


------------------------------------------------------------------------

## Features

-   Typed API facade (`VarioApi`)
-   Domain-driven read models (KnownParty, Product)
-   Immutable value objects
-   DatasetView → Domain mapping layer
-   Automatic authentication & token refresh
-   PSR‑18 HTTP client support
-   PSR‑3 logging support
-   Structured request/response logging
-   Request duration measurement
-   Replaceable HTTP adapter (no vendor lock‑in)
-   Configurable token storage (memory, session, custom)
-   Lazy‑loaded API modules
-   Unified exception model
-   PHPStan **level max** ready

------------------------------------------------------------------------

## Requirements

-   PHP 8.1+
-   Composer
-   Access to Vario Online API (VPN + credentials)
-   PSR‑18 HTTP client implementation
-   PSR‑3 compatible logger

------------------------------------------------------------------------

## Installation

Install the SDK via Composer:

``` bash
composer require lemonade/vario-online-sdk
```

The SDK itself does **not ship with a built‑in HTTP client**.

Instead, it follows the **PSR‑18 HTTP Client standard**, which allows
you to use any compatible HTTP client implementation.

Example using **Guzzle**:

``` bash
composer require guzzlehttp/guzzle guzzlehttp/psr7
```

------------------------------------------------------------------------

## Quick Start

``` php
use Lemonade\Vario\VarioApiFactory;
use Lemonade\Vario\VarioClientConfig;
use Lemonade\Vario\ValueObject\DatasetViewQuery;
use Lemonade\Vario\ValueObject\CustomDatasetView;
use Lemonade\Vario\Http\Adapter\GuzzleHttpAdapter;
use Lemonade\Vario\Auth\Storage\InMemoryTokenStorage;

$config = new VarioClientConfig(
    baseUrl: 'https://your-vario-server',
    loginName: 'USER',
    password: 'PASSWORD',
    companyNumber: 'COMPANY'
);

$vario = VarioApiFactory::create(
    config: $config,
    httpAdapter: new GuzzleHttpAdapter($config),
    tokenStorage: new InMemoryTokenStorage()
);

$query = DatasetViewQuery::for(
    new CustomDatasetView('Katalog/KatalogCenikAPI'),
    pageLength: 100
);

$data = $vario->datasetView()->get($query);
```

------------------------------------------------------------------------
## Domain Example — Known Parties

The SDK exposes a typed **KnownParty domain model** representing
business subjects stored in the Vario contact register.

Instead of working with raw API payloads, integrations interact with
strongly-typed objects and immutable value objects.

``` php
use Lemonade\Vario\ValueObject\KnownPartyQuery;

$items = $vario->knownParties()->query(
    new KnownPartyQuery(pageLength: 15)
);

foreach ($items as $party) {
    echo $party->getName();
    echo $party->getCityLine();
    echo $party->getCompanyNumber();
}
```

### Domain helpers

The `KnownParty` entity provides semantic helpers for commonly used
data:

-   `getDisplayAddress()`
-   `getCityLine()` / `getStreetLine()`
-   `getCompanyNumber()`
-   `getVatId()`
-   `getIdentificationsOrEmpty()`

This eliminates the need to manually parse API payload arrays.

------------------------------------------------------------------------

### Creating or Updating Known Parties

The SDK provides a dedicated **write model** (`KnownPartyInput`) for
creating or updating contacts.

``` php
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\KnownParty\KnownPartyKind;
use Lemonade\Vario\Domain\Shared\PostalAddress;
use Lemonade\Vario\Domain\Shared\Identification;
use Lemonade\Vario\Domain\Shared\IdentificationScheme;

$input = (new KnownPartyInput('Test Company'))
    ->withKind(KnownPartyKind::Organization)
    ->withEmail('info@example.com')
    ->withTelephone('+420123456789')
    ->withAddress(new PostalAddress(
        street: 'Main Street',
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
```

------------------------------------------------------------------------

### Preview request payload

Before sending a request to the API, the payload can be previewed. This
is useful for debugging integrations or verifying request structure.

``` php
$preview = $vario->knownParties()->previewUpsert([$input]);

print_r($preview);
```

Example payload:

``` text
[
  [
    "Name" => "Test Company",
    "ElectronicMail" => "info@example.com",
    "PostalAddress" => [
        "StreetName" => "Main Street",
        "BuildingNumber" => "10",
        "CityName" => "Prague",
        "PostalZone" => "11000",
        "CountryIso" => "CZ"
    ]
  ]
]
```

------------------------------------------------------------------------

### Performing the upsert request

``` php
$result = $vario->knownParties()->upsert([$input]);

foreach ($result as $r) {
    echo $r->getUuid();
}
```

The API returns confirmation objects:

``` php
KnownPartyUpsertResult
```

Each result contains:

-   `UUID`
-   `RecipientObjectID`

These values identify the created or updated entity.

------------------------------------------------------------------------

### Architecture

The KnownParty module separates **read models**, **write models** and
**transport normalization**.

    Domain
     ├─ KnownParty
     │   ├─ KnownParty
     │   ├─ KnownPartyInput
     │   ├─ KnownPartyKind
     │   └─ KnownPartyUpsertResult
     │
     └─ Shared
         ├─ PostalAddress
         ├─ Identification
         ├─ IdentificationCollection
         └─ IdentificationScheme

    Normalizer
     └─ KnownParty
         └─ KnownPartyInputNormalizer

    Mapper
     └─ KnownParty
         └─ KnownPartyMapper

This separation ensures:

-   clean domain models
-   transport‑independent business logic
-   safe request serialization
-   strict typing compatible with **PHPStan level max**
------------------------------------------------------------------------
## Domain Example — Products

The SDK provides a typed **Product domain model** for working with
catalogue data exposed through Vario `DatasetView` endpoints.

Products are composed from individual **ProductSection** objects
(identity, pricing, inventory, etc.) which are mapped from DatasetView
rows using a configurable mapper pipeline.

```php
use Lemonade\Vario\Domain\Product\ProductDatasetMapping;
use Lemonade\Vario\Domain\Product\ProductMapper;
use Lemonade\Vario\Domain\Product\Mapper\ProductIdentityMapper;
use Lemonade\Vario\Domain\Product\Mapper\ProductPricingMapper;
use Lemonade\Vario\Domain\Product\Mapping\ProductIdentityMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductPricingMapping;

$mapping = (new ProductDatasetMapping())
    ->add(new ProductIdentityMapper(
        new ProductIdentityMapping(
            uuid: 'ID_Produktu',
            sku: 'Produkt',
            name: 'Nazev_produktu'
        )
    ))
    ->add(new ProductPricingMapper(
        new ProductPricingMapping(
            price: 'Cena_Cenik'
        )
    ));

$mapper = new ProductMapper($mapping);

foreach ($mapper->iterate($response['Data'] ?? []) as $product) {
    echo $product->identity()?->getName();
    echo $product->pricing()?->getPrice();
}
```

### Processing Strategies

The `ProductMapper` supports multiple result processing strategies
depending on memory requirements and dataset size.

| Method | Result type | Description |
|------|------|------|
| `iterate()` | generator | streaming processing (lowest memory usage) |
| `collect()` | `ProductCollection` | materialized collection |
| `lazy()` | `LazyProductCollection` | lazy processing pipeline (re-iterable) |

The `lazy()` strategy returns a `LazyProductCollection`, which builds a
processing pipeline over the mapped products.

Unlike a plain generator, the lazy collection **can be iterated multiple times**
and supports pipeline operations such as `filter()` before materialization.

Example:

```php
$products = $mapper
    ->lazy($response['Data'] ?? [])
    ->filter(fn($p) => $p->inventory()?->getStock() > 0);

foreach ($products as $product) {
    echo $product->identity()?->getName();
}
```

### Custom Product Sections

The product mapping pipeline is **fully extensible**.

Applications can introduce additional product sections by implementing
`ProductSectionMapper`. This makes it possible to map fields that are
specific to a particular Vario installation without modifying the SDK
core.

Each custom mapper extracts a subset of fields from the `DatasetRow` and
converts them into a strongly‑typed `ProductSection` value object.

Example custom mapper:

``` php
final class ProductSeoMapper extends AbstractProductSectionMapper
{
    public function __construct(
        private readonly ProductSeoMapping $mapping
    ) {}

    public function map(DatasetRow $row): ?ProductSeo
    {
        $title = $this->mapString($row, $this->mapping->getTitle());
        $description = $this->mapString($row, $this->mapping->getDescription());

        if ($title === null && $description === null) {
            return null;
        }

        return new ProductSeo($title, $description);
    }
}
```

Register the mapper in `ProductDatasetMapping`:

``` php
$mapping->add(
    new ProductSeoMapper(
        new ProductSeoMapping(
            title: 'SEO_Title',
            description: 'SEO_Description'
        )
    )
);
```

Once registered, the section becomes part of the product mapping
pipeline and can be accessed like any other product section:

``` php
$seo = $product->get(ProductSeo::class);
```

The `Product::get()` method retrieves the mapped section instance by its
class type. If the mapping pipeline did not produce the requested
section, the method returns `null`.

### Full Example

A complete working example demonstrating:

- mapper configuration
- streaming iteration
- materialized collections
- lazy processing pipelines
- accessing all product sections

is available in:

```
examples/datasetview-products.php
```

### Architecture

The product mapping pipeline converts raw **DatasetView responses**
into a typed Product domain model composed of independent sections.

    DatasetView response
        ↓
    DatasetRow
        ↓
    ProductSectionMapper pipeline
        ↓
    ProductSection (ValueObject)
        ↓
    Product (aggregate)
        ↓
    iterate() / collect() / lazy()
        ↓
    ProductCollection / LazyProductCollection

Each mapper extracts a specific part of the dataset row and converts it
into a strongly-typed `ProductSection` value object.

Built-in section mappers include:

- `ProductIdentityMapper`
- `ProductPricingMapper`
- `ProductInventoryMapper`
- `ProductFlagsMapper`
- `ProductDimensionsMapper`
- `ProductAttributesMapper`

The mapping pipeline is fully extensible.  
Additional product sections can be introduced by implementing
`ProductSectionMapper` and registering the mapper in
`ProductDatasetMapping`.

This architecture allows the SDK to remain:

- **extensible**
- **strongly typed**
- independent from DatasetView column names
- compatible with **PHPStan level max**

The separation between mapping and processing also allows applications
to efficiently handle very large product catalogues without loading the
entire dataset into memory.
------------------------------------------------------------------------


## Filtering & Sorting

Queries support paging, sorting and composable filters.

### Basic filtering

``` php
use Lemonade\Vario\ValueObject\KnownPartyQuery;
use Lemonade\Vario\Query\QueryFilters;

$query = (new KnownPartyQuery())
    ->withPageLength(50)
    ->withSort('Name')
    ->withFilter(
        QueryFilters::equals('ElectronicMail', 'email@address.com')
    );

$items = $vario->knownParties()->query($query);
```

**Note:**Filtering is limited to properties exposed by the Vario API endpoint.
Only fields that are **supported by the backend DatasetView** can be used in `FilterCriteria`.

For example:
``` text
ElectronicMail
Name
UUID
Kind
```

Nested or computed fields may not be filterable unless the backend explicitly supports them.

### Multiple filters

``` php
$query = (new KnownPartyQuery())
    ->withPageLength(50)
    ->withFilter(
        QueryFilters::equals('Kind', 'Organization')
    )
    ->withFilter(
        QueryFilters::like('Name', 'Mapri%')
    );
```

------------------------------------------------------------------------
## Configuration

### Manual configuration

```php
$config = new VarioClientConfig(
    baseUrl: 'https://server',
    loginName: 'USER',
    password: 'PASSWORD',
    companyNumber: 'COMPANY'
);

$vario = VarioApiFactory::create(
    config: $config,
    httpAdapter: new GuzzleHttpAdapter($config),
    tokenStorage: new InMemoryTokenStorage()
);

```

### Environment configuration

```php
$vario = VarioApiFactory::create(
    VarioClientConfig::fromEnv(),
    new GuzzleHttpAdapter(VarioClientConfig::fromEnv())
);
```

Required environment variables:

    VARIO_URL
    VARIO_LOGIN
    VARIO_PASSWORD
    VARIO_COMPANY
------------------------------------------------------------------------

## Logging

The SDK supports **PSR‑3 compatible logging**.

All API requests and responses are logged using the configured logger.

Logged metadata includes:

-   HTTP method
-   endpoint URI
-   query string
-   authorization presence
-   request/response payload size
-   response status code
-   request duration (milliseconds)

### Example log entry (Monolog default)

When using Monolog with the default **LineFormatter**, logs are written
as text lines:

``` text
[2026-03-05T10:40:27.123456+00:00] vario.DEBUG: Vario API response {"status":200,"uri":"/Api/GetDatasetView","query":"Agenda=Katalog&Pager.PageLength=50","content_length":2917,"duration_ms":178.28} []
```

Structure:

    [timestamp] channel.level: message context extra

-   **context** contains metadata logged by the SDK\
-   **extra** contains data added by logger processors

### Providing a logger

``` php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('vario');
$logger->pushHandler(new StreamHandler(__DIR__.'/vario.log'));

$config = new VarioClientConfig(
    baseUrl: 'https://server',
    loginName: 'USER',
    password: 'PASSWORD',
    companyNumber: 'COMPANY',
    logger: $logger
);
```

If no logger is provided, the SDK falls back to:

    Psr\Log\NullLogger

------------------------------------------------------------------------

### Structured logging (optional)

For structured logs the SDK provides optional Monolog helpers:

-   `HybridRotatingFileHandler`
-   `FlatJsonFormatter`

These enable **JSON logging with automatic rotation**.

``` php
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\MemoryPeakUsageProcessor;
use Monolog\Processor\ProcessIdProcessor;

use Lemonade\Vario\Logging\Monolog\HybridRotatingFileHandler;
use Lemonade\Vario\Logging\Monolog\FlatJsonFormatter;

$logger = new Logger('vario');

$handler = new HybridRotatingFileHandler(
    logDirectory: __DIR__ . '/logs',
    channel: 'vario',
    maxFileSizeBytes: 10 * 1024 * 1024
);

$handler->setFormatter(new FlatJsonFormatter());

$logger->pushHandler($handler);
$logger->pushProcessor(new UidProcessor());
$logger->pushProcessor(new MemoryUsageProcessor());
$logger->pushProcessor(new MemoryPeakUsageProcessor());
$logger->pushProcessor(new ProcessIdProcessor());
```

Example JSON log entry:

``` json
{
  "message": "Vario API response",
  "status": 200,
  "uri": "/Api/GetDatasetView",
  "query": "Agenda=Katalog&Pager.PageLength=50",
  "content_length": 2917,
  "duration_ms": 178.28
}
```



------------------------------------------------------------------------
## Token Storage

The SDK uses pluggable token storage implementations.\
A storage implementation is responsible for persisting the **access
token** received during authentication.

All storage backends implement:

`TokenStorageInterface`

### Built-in implementations

The SDK currently provides the following token storage implementations:

-   **InMemoryTokenStorage**\
    Stores the token only in memory. Suitable for CLI scripts or
    short-lived processes.

-   **FileTokenStorage**\
    Persists the token to a file on disk. Useful for cron jobs or
    integrations where the process runs repeatedly.

-   **SessionTokenStorage**\
    Stores the token inside a session implementation.

-   **RedisTokenStorage**\
    Stores the token in Redis. Suitable for distributed environments or
    multiple workers.

------------------------------------------------------------------------

## Session abstraction

The SDK does **not depend directly on native PHP sessions**.

Instead it defines a minimal session abstraction:

`SessionInterface`

``` php
interface SessionInterface
{
    public function get(string $key): mixed;
    public function set(string $key, mixed $value): void;
    public function has(string $key): bool;
    public function remove(string $key): void;
    public function isActive(): bool;
}
```

This allows integrating the SDK with:

-   native PHP sessions
-   framework session services (Symfony, Laravel, Nette)
-   custom session backends

A default implementation is provided:

`NativePhpSession`

which wraps the standard `$_SESSION` storage.

------------------------------------------------------------------------

## Example - Session token storage

``` php
use Lemonade\Vario\Auth\Session\NativePhpSession;
use Lemonade\Vario\Auth\Storage\SessionTokenStorage;

$tokenStorage = new SessionTokenStorage(
    new NativePhpSession()
);
```

------------------------------------------------------------------------

## Example - In-memory storage (CLI)

``` php
use Lemonade\Vario\Auth\Storage\InMemoryTokenStorage;

$tokenStorage = new InMemoryTokenStorage();
```

------------------------------------------------------------------------

## Using token storage in the SDK

A storage implementation must be provided when creating the API client:

``` php
use Lemonade\Vario\VarioApiFactory;
use Lemonade\Vario\Auth\Storage\InMemoryTokenStorage;

$vario = VarioApiFactory::create(
    $config,
    new GuzzleHttpAdapter($config),
    new InMemoryTokenStorage()
);
```

------------------------------------------------------------------------

### Recommended usage

Environment        Recommended storage
  ------------------ ------------------------
CLI scripts        `InMemoryTokenStorage`
Cron jobs          `FileTokenStorage`
Web apps           `SessionTokenStorage`
Distributed apps   `RedisTokenStorage`

------------------------------------------------------------------------

## Authentication Lifecycle

Authentication is handled automatically:

1.  Initial authentication during factory creation
2.  Access token stored internally
3.  When API returns **401** the client:
   -   re‑authenticates
   -   retries the request once
4.  Second failure throws `AuthenticationException`

------------------------------------------------------------------------

## Architecture

The SDK follows a layered architecture:

    VarioApi (Facade)
            ↓
    API Modules
            ↓
    Mapper layer
            ↓
    Domain Models
            ↓
    VarioClient (transport + retry + auth)
            ↓
    HttpAdapterInterface (PSR boundary)
            ↓
    HTTP client implementation

------------------------------------------------------------------------

## Development

Run static analysis:

``` bash
composer stan
```

Run tests:

``` bash
composer test
```

------------------------------------------------------------------------

## License

MIT License

Copyright (c) 2026 Jan Mudrák
