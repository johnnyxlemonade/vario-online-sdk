# Vario Online API PHP SDK (Community Vario ERP Integration Library)

[![PHP
Version](https://img.shields.io/packagist/php-v/lemonade/vario-online-sdk)](https://packagist.org/packages/lemonade/vario-online-sdk)
[![Packagist
Version](https://img.shields.io/packagist/v/lemonade/vario-online-sdk)](https://packagist.org/packages/lemonade/vario-online-sdk)
[![Downloads](https://img.shields.io/packagist/dt/lemonade/vario-online-sdk)](https://packagist.org/packages/lemonade/vario-online-sdk)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%2010-brightgreen.svg)](https://phpstan.org/)
[![Tests](https://github.com/johnnyxlemonade/vario-online-sdk/actions/workflows/phpunit.yml/badge.svg)](https://github.com/johnnyxlemonade/vario-online-sdk/actions/workflows/phpunit.yml)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

A strongly‑typed **PHP SDK for the Vario Online API**, designed for
building custom integrations with **Vario ERP systems**.

This project is an **independent community‑maintained client library**
for developers who want to integrate PHP applications with the **Vario
ERP platform**. It is **not officially affiliated with Vario Software**.

The SDK provides a modern domain‑oriented abstraction layer over the
Vario Online API including dataset queries, authentication handling,
domain mapping and transport‑agnostic HTTP communication.

It is designed for building **stable ERP integrations** where API
payload structures may vary between installations.

More information about Vario ERP:\
https://vario.cz/


---
## Typical Use Cases

This SDK is commonly used for:

-   integrating **Vario ERP with e‑commerce platforms**
-   synchronizing **products, customers and orders**
-   building **data pipelines from Vario datasets**
-   exporting ERP data into BI or analytics systems
-   creating **custom ERP dashboards or integrations**
-   automating ERP workflows

---

## Why this SDK exists

The official **Vario Online API** exposes data primarily as loosely
structured array responses tied directly to backend fields.

While flexible, this creates several challenges:

-   dataset column names may differ between installations
-   integrations require manual array parsing
-   business logic becomes tightly coupled to API payload structure

This SDK introduces a **domain‑oriented integration layer**.

Instead of working with raw arrays, developers interact with **typed
domain models** such as:

-   `KnownParty`
-   `Product`
-   `Inventory`

These models are backed by immutable value objects and a configurable
mapping layer that isolates application logic from the underlying API
schema.

Goals of the SDK:

-   stable integration surface
-   strong typing and IDE support
-   separation between API payloads and application logic
-   maintainable ERP integrations

---

## Features

- **Typed API facade** (`VarioApi`)
- **Domain-driven read models** (KnownParty, Product)
- Immutable value objects
- **DatasetView → Domain mapping layer**
- **Streaming dataset processing** (`iterate()`, lazy pipelines)
- **Automatic authentication & token refresh**
- **PSR-18 transport layer** (replaceable HTTP client, no vendor lock-in)
- PSR-3 logging support
- Structured request/response logging with duration metrics
- Configurable token storage (memory, session, custom)
- Lazy-loaded API modules
- Unified exception model
- **PHPStan level 10 compliant**

---
## Supported Vario API Areas

The SDK provides abstractions for several Vario Online API modules:

-   DatasetView API
-   KnownParty API
-   Product catalog datasets
-   Incoming orders
-   Outgoing invoices

Additional integrations can be built through the generic DatasetView
layer.

---
## Architecture Overview

The SDK is designed as a **layered architecture** that separates application usage, API modules, domain mapping, and transport infrastructure.

Each layer has a clearly defined responsibility, which allows the SDK to remain stable even when the underlying API or HTTP client implementation changes.

The main architectural goals are:

- keep **application code independent from transport details**
- provide **typed domain models instead of raw API arrays**
- isolate **API schema volatility behind a mapping layer**
- allow **replaceable HTTP clients through PSR-18**

```
Application
  │
  ▼
VarioApiFactory
  │
  ├─ VarioClientConfig
  ├─ TokenStorageInterface
  └─ PSR-18 Client Discovery
  │
  ▼
VarioApi (Facade)
  │
  ▼
API Modules
  │
  ├─ Dataset APIs (Queries)
  │     ▼
  │   Mapper / Normalizer Layer
  │     ▼
  │   Domain Read Models (Product, KnownParty…)
  │
  └─ Endpoint APIs (Actions)
        ▼
      Domain Entities / Responses
  │
  ▼
VarioClient (Transport Orchestrator)
  │
  ├─ RequestAuthenticator
  ├─ RequestLogger
  └─ ResponseHandler
  │
  ▼
PSR-18 HTTP Client Interface
  │
  ▼
External HTTP Client
(Guzzle / Symfony HttpClient / Nyholm PSR-7 / Laminas Diactoros / custom)

```
The diagram illustrates the logical architecture layers of the SDK rather than the exact runtime request flow.

Applications interact with the SDK through the `VarioApi` facade created by the `VarioApiFactory`.
The factory assembles the complete client stack including configuration,
token storage and HTTP transport.

### API Modules

The SDK exposes functionality through **API modules**, each responsible for a specific area of the Vario API.

Two integration styles are supported:

**Dataset APIs (Queries)**  
These APIs retrieve data from Vario datasets. The responses are normalized and mapped into **domain read models**.  
This layer isolates application logic from dataset column naming and schema changes.

**Endpoint APIs (Actions)**
These APIs interact with specific Vario endpoints such as order creation,
entity updates or other write operations.
They typically return domain entities or response objects directly without passing through the dataset mapping layer.

### Mapping Layer

For dataset-based integrations the SDK introduces a **mapping and normalization layer**.

This layer transforms raw DatasetView rows into stable domain models such as:

- `Product`
- `KnownParty`
- `Inventory`

By separating mapping logic from application code, changes in the Vario dataset schema usually require modifying only the mapper configuration rather than the business logic.

### Transport Layer

The actual communication with the Vario API is orchestrated by `VarioClient`.

The client coordinates:

- authentication and token refresh
- request logging
- response handling and error conversion

The transport boundary follows the **PSR-18 HTTP Client standard**, allowing developers to use any compatible HTTP client implementation such as Guzzle or Symfony HttpClient.

This design ensures that **transport concerns never leak into domain logic**, keeping integrations maintainable and testable.

---

## Strategic Design Decisions (ADR)

The development of this SDK was guided by specific architectural principles to ensure enterprise-grade stability and developer experience:

### 1. Zero-Mixed Policy (PHPStan Level 10)
Unlike most SDKs that rely on `array<mixed>` and "hope for the best", this library enforces **Level 10 static analysis** with `strict-rules` and `bleedingEdge`. Every API response is mapped through strict **Array Shapes**. This shifts 90% of potential integration bugs from *runtime* to the *static analysis* phase. If the code passes `composer stan`, the data structure is guaranteed.

### 2. Memory-First Streaming & Lazy Pipelines
Vario datasets often contain tens of thousands of records. Most integrations fail here due to `Memory Exhausted` errors. This SDK implements **Streaming Generators** (`iterate()`) and **Lazy Pipelines** as first-class citizens. You can process a 100k+ product catalog on a server with minimal RAM, as only one domain record exists in memory at any given time during iteration.

### 3. Domain-Oriented Mapping Layer
The Vario API schema is volatile and often varies between installations. This SDK solves this by introducing a **Mapping Layer**. Your application logic stays coupled to stable, immutable **Domain Models** (`Product`, `KnownParty`, `Inventory`), while the SDK handles the translation from Vario's backend fields. Changing a column name in Vario requires only a 1-line change in the mapper, not a rewrite of your business logic.

### 4. Transport Agnostic (PSR-18)
By strictly following **PSR-18** and **PSR-17**, the SDK eliminates vendor lock-in. Whether your project uses Guzzle, Symfony HttpClient, or a custom wrapper, the SDK remains stable. This boundary ensures that transport-level concerns (like SSL issues or proxy settings) never leak into your domain mapping logic.

---

## Roadmap

The SDK will continue evolving with improvements focused on
extensibility, performance and maintainability.

### Middleware Pipeline

Introduce a **request/response middleware pipeline** to reduce
responsibilities currently handled inside `VarioClient`.

Planned processing flow:

```
Request
  ↓
AuthenticationMiddleware
  ↓
LoggingMiddleware
  ↓
RetryMiddleware
  ↓
HTTP Client
  ↓
ResponseMiddleware
  ↓
Result
```

Benefits:

- smaller and simpler `VarioClient`
- clearer separation of transport concerns
- easier testing
- support for custom middleware

### Dataset Streaming Improvements

Enhancements to large dataset processing:

- improved lazy pipelines
- configurable page prefetching
- better memory diagnostics

### Additional Domain Modules

Future domain abstractions may include:

- inventory
- warehouse operations
- sales documents
- customer pricing

---

## Requirements

-   PHP 8.1+
-   Composer
-   Access to Vario Online API (VPN + credentials)
-   PSR‑18 HTTP client implementation
-   PSR‑3 compatible logger

---

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

---

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

// Example uses Guzzle as the HTTP transport.
// Any PSR-18 compatible HTTP client can be used instead.
$vario = VarioApiFactory::create(
    config: $config,
    httpAdapter: new GuzzleHttpAdapter($config),
    tokenStorage: new InMemoryTokenStorage()
);

$query = DatasetViewQuery::for(
    new CustomDatasetView('Katalog/KatalogCenikAPI'),
    pageLength: 100
);

// Stream dataset rows directly from the Vario API:
foreach ($vario->datasetView()->iterate($query) as $row) {
    print_r($row);
}
```
### Mapping dataset rows to domain models

The mapping layer converts raw dataset rows into strongly typed domain objects.

```php
foreach ($mapper->iterate($vario->datasetView()->iterate($productQuery)) as $product) {

    $identity = $product->identity();
    $price = $product->pricing()?->getPrice();

    echo $identity?->getSku() . ' | ';
    echo $identity?->getName() . ' | ';
    echo $price?->getValue() . PHP_EOL;
}
```

Full examples are available in the `examples/` directory.

---
## Documentation

Full documentation is available in the project Wiki:

https://github.com/johnnyxlemonade/vario-online-sdk/wiki

Key documentation pages:

- [Getting Started](https://github.com/johnnyxlemonade/vario-online-sdk/wiki/Getting-Started)
- [Installation](https://github.com/johnnyxlemonade/vario-online-sdk/wiki/Installation)
- [Configuration](https://github.com/johnnyxlemonade/vario-online-sdk/wiki/Configuration)
- [KnownParty Domain](https://github.com/johnnyxlemonade/vario-online-sdk/wiki/KnownParty-Domain)
- [Product Domain](https://github.com/johnnyxlemonade/vario-online-sdk/wiki/Product-Domain)
- [Filtering & Queries](https://github.com/johnnyxlemonade/vario-online-sdk/wiki/Filtering-and-Queries)
- [Logging](https://github.com/johnnyxlemonade/vario-online-sdk/wiki/Logging)
- [Token Storage](https://github.com/johnnyxlemonade/vario-online-sdk/wiki/Token-Storage)
- [Authentication](https://github.com/johnnyxlemonade/vario-online-sdk/wiki/Authentication)
- [Architecture](https://github.com/johnnyxlemonade/vario-online-sdk/wiki/Architecture)

---

## Development

Run static analysis:

``` bash
composer stan
```

Run tests:

``` bash
composer test
```

---
## Disclaimer

This project is an independent open‑source initiative and is not
affiliated with or endorsed by Vario Software.

---

## License

MIT License

Copyright (c) 2026 Jan Mudrák
