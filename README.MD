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

The SDK exposes typed domain entities instead of raw arrays.

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

Entities provide semantic helpers:

- `getDisplayAddress()`
- `getCityLine()` / `getStreetLine()`
- `getCompanyNumber()`
- `getVatId()`
- `getIdentificationsOrEmpty()`

No manual array parsing required.

------------------------------------------------------------------------
## Domain Example --- Products

The SDK provides a typed **Product domain model** for working with
catalogue data exposed through Vario `DatasetView` endpoints.

Dataset rows are mapped into a normalized `Product` aggregate using a
set of `ProductSectionMapper` implementations. Each mapper is
responsible for mapping one section of the product (identity, pricing,
inventory, etc.).

``` php
use Lemonade\Vario\Domain\Product\ProductDatasetMapping;
use Lemonade\Vario\Domain\Product\ProductMapper;
use Lemonade\Vario\Domain\Product\Mapper\ProductIdentityMapper;
use Lemonade\Vario\Domain\Product\Mapper\ProductPricingMapper;
use Lemonade\Vario\Domain\Product\Mapping\ProductIdentityMapping;
use Lemonade\Vario\Domain\Product\Mapping\ProductPricingMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductIdentity;
use Lemonade\Vario\Domain\Product\ValueObject\ProductPricing;
use Lemonade\Vario\ValueObject\DatasetViewQuery;
use Lemonade\Vario\ValueObject\CustomDatasetView;

$query = DatasetViewQuery::for(
    new CustomDatasetView('Katalog/KatalogCenikAPI'),
    pageLength: 50
);

$response = $vario->datasetView()->get($query);

$mapping = new ProductDatasetMapping();

$mapping->register(
    new ProductIdentityMapper(
        new ProductIdentityMapping(
            uuid: 'ID_Produktu',
            sku: 'Produkt',
            name: 'Nazev_produktu'
        )
    )
);

$mapping->register(
    new ProductPricingMapper(
        new ProductPricingMapping(
            price: 'Cena_Cenik'
        )
    )
);

$mapper = new ProductMapper($mapping);

foreach ($mapper->iterate($response['Data'] ?? []) as $product) {

    $identity = $product->get(ProductIdentity::class);
    $pricing = $product->get(ProductPricing::class);

    // convenience helpers for core sections
    $identity = $product->getIdentity();
    $pricing = $product->getPricing();

    echo $identity?->getName();
    echo $pricing?->getPrice();
}
```

### Custom Product Sections
The product mapping pipeline is fully extensible.  
You can register your own product sections by implementing
`ProductSectionMapper`.

```php
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
```php
$mapping->register(
    new ProductSeoMapper(
        new ProductSeoMapping(
            title: 'SEO_Title',
            description: 'SEO_Description'
        )
    )
);
```

Then access it like any other product section:

```php
$seo = $product->get(ProductSeo::class);
```

### Architecture

    DatasetRow
       ↓
    ProductSectionMapper (customizable)
       ↓
    ProductSection (ValueObject)
       ↓
    Product (aggregate)

Each mapper converts a dataset row into a specific product section:

-   `ProductIdentityMapper`
-   `ProductPricingMapper`
-   `ProductInventoryMapper`
-   `ProductFlagsMapper`
-   `ProductDimensionsMapper`
-   `ProductAttributesMapper`

This architecture allows the SDK to remain extensible, strongly typed,
and compatible with PHPStan level max.

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
