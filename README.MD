# Vario Online API PHP SDK (Community Vario ERP Integration Library)

[![PHP
Version](https://img.shields.io/packagist/php-v/lemonade/vario-online-sdk)](https://packagist.org/packages/lemonade/vario-online-sdk)
[![Packagist
Version](https://img.shields.io/packagist/v/lemonade/vario-online-sdk)](https://packagist.org/packages/lemonade/vario-online-sdk)
[![Downloads](https://img.shields.io/packagist/dt/lemonade/vario-online-sdk)](https://packagist.org/packages/lemonade/vario-online-sdk)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%2010-brightgreen.svg)](https://phpstan.org/)
[![Tests](https://github.com/johnnyxlemonade/vario-online-sdk/actions/workflows/phpunit.yml/badge.svg)](https://github.com/johnnyxlemonade/vario-online-sdk/actions/workflows/phpunit.yml)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

A stronglyâ€‘typed **PHP SDK for the Vario Online API**, designed for
building custom integrations with **Vario ERP systems**.

This project is an **independent communityâ€‘maintained client library**
for developers who want to integrate PHP applications with the **Vario
ERP platform**. It is **not officially affiliated with Vario Software**.

The SDK provides a modern domainâ€‘oriented abstraction layer over the
Vario Online API including dataset queries, authentication handling,
domain mapping and transportâ€‘agnostic HTTP communication.

It is designed for building **stable ERP integrations** where API
payload structures may vary between installations.

More information about Vario ERP:\
https://vario.cz/


---
## Typical Use Cases

This SDK is commonly used for:

-   integrating **Vario ERP with eâ€‘commerce platforms**
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

This SDK introduces a **domainâ€‘oriented integration layer**.

Instead of working with raw arrays, developers interact with **typed
domain models** such as:

-   `KnownParty`
-   `Product`
-   `IncomingOrder`

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
- **DatasetView â†’ Domain mapping layer**
- **Streaming dataset processing** (`iterate()`, lazy pipelines)
- **IncomingOrder builder** with shared document calculated lines and automatic line/total calculation
- **OutgoingQuotation builder/write flow** with payload preview and upsert support
- **Shared document primitives and calculators** for document-like endpoints (`Domain\Shared\Document`)
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
-   Incoming orders (query, mapping, payload preview, builder-based upsert flow)
-   Outgoing quotations (payload preview, builder-based upsert flow)

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
  â”‚
  â–Ľ
VarioApiFactory
  â”‚
  â”śâ”€ VarioClientConfig
  â”śâ”€ TokenStorageInterface
  â””â”€ PSR-18 Client Discovery
  â”‚
  â–Ľ
VarioApi (Facade)
  â”‚
  â–Ľ
API Modules
  â”‚
  â”śâ”€ Dataset APIs (Queries)
  â”‚     â–Ľ
  â”‚   Mapper / Normalizer Layer
  â”‚     â–Ľ
  â”‚   Domain Read Models (Product, KnownPartyâ€¦)
  â”‚
  â””â”€ Endpoint APIs (Actions)
        â–Ľ
      Domain Entities / Responses
  â”‚
  â–Ľ
VarioClient (Transport Orchestrator)
  â”‚
  â”śâ”€ RequestAuthenticator
  â”śâ”€ RequestLogger
  â””â”€ ResponseHandler
  â”‚
  â–Ľ
PSR-18 HTTP Client Interface
  â”‚
  â–Ľ
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

### Shared document model

Document-like endpoints share a common foundation under
`Lemonade\Vario\Domain\Shared\Document`.

This shared layer includes:

- `DocumentCalculatedLineInput`
- `DocumentPriceMode`
- `DocumentDescription`
- monetary totals
- tax totals
- tax exchange rate
- line and totals calculators

Endpoint-specific root models remain separate where they represent actual endpoint semantics:

- `IncomingOrderInput`
- `OutgoingQuotationInput`
- endpoint-specific API modules
- endpoint-specific result objects

### Mapping Layer

For dataset-based integrations the SDK introduces a **mapping and normalization layer**.

This layer transforms raw DatasetView rows into stable domain models such as:

- `Product`
- `KnownParty`
- `IncomingOrder`

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
  â†“
AuthenticationMiddleware
  â†“
LoggingMiddleware
  â†“
RetryMiddleware
  â†“
HTTP Client
  â†“
ResponseMiddleware
  â†“
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
-   PSRâ€‘18 HTTP client implementation
-   PSRâ€‘3 compatible logger

---

## Installation

Install the SDK via Composer:

``` bash
composer require lemonade/vario-online-sdk
```

The SDK itself does **not ship with a builtâ€‘in HTTP client**.

Instead, it follows the **PSRâ€‘18 HTTP Client standard**, which allows
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

## Domain Example â€” Incoming Orders

The SDK provides a typed **IncomingOrder module** for both reading and writing incoming order documents.

For write operations, the recommended entry point is `IncomingOrderBuilder`.

It allows developers to define order lines using a simplified high-level input and automatically derives:

- line totals without VAT
- line totals with VAT
- tax subtotals
- document monetary total
- document tax total

This reduces the amount of manual low-level payload construction needed for integrations.

### Recommended write flow

```php
use DateTimeImmutable;
use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\IncomingOrder\Builder\IncomingOrderBuilder;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPaymentMeansCode;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineItemInput;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentPriceMode;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineInput;

$builder = new IncomingOrderBuilder();

$lineItem = (new IncomingOrderLineItemInput())
    ->withCatalogueItemIdentification('SKU-001')
    ->withSellersItemIdentification('SKU-001');

$order = $builder->build(
    uuid: 'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
    issueDate: new DateTimeImmutable('2024-04-02T00:00:00+02:00'),
    currency: Currency::CZK,
    buyerCustomerParty: $buyer,
    sellerSupplierParty: $seller,
    lines: [
        new DocumentCalculatedLineInput(
            uuid: 'd2045e34-49b4-4238-84e2-950362f2007e',
            lineItem: $lineItem,
            quantity: 2.0,
            unitCode: 'Ks',
            unitPrice: 100.0,
            vatRate: 21.0,
            priceMode: DocumentPriceMode::WithoutVat,
        ),
    ],
    paymentMeansCode: IncomingOrderPaymentMeansCode::BankAccount,
);

$preview = $vario->incomingOrders()->previewUpsert([$order]);

print_r($preview);
```

## Domain Example — Outgoing Quotations

`OutgoingQuotationBuilder` uses the same shared document model.
You define high-level calculated lines, the builder derives line amounts,
monetary totals and tax totals, and `previewUpsert()` returns the exact payload.

```php
use DateTimeImmutable;
use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Builder\OutgoingQuotationBuilder;
use Lemonade\Vario\Domain\OutgoingQuotation\Enum\OutgoingQuotationPaymentMeansCode;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationLineItemInput;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentPriceMode;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentDescription;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineInput;
use Lemonade\Vario\Domain\Shared\Identification;
use Lemonade\Vario\Domain\Shared\IdentificationScheme;

$buyer = (new KnownPartyInput('A - Storex, v.o.s.'))
    ->addIdentification(new Identification(IdentificationScheme::UIN, '620927153', 'CZ'));

$seller = (new KnownPartyInput(''))
    ->addIdentification(new Identification(IdentificationScheme::VAT, 'CZ61681229', 'CZ'));

$quotation = (new OutgoingQuotationBuilder())->build(
    uuid: 'c676048c-3789-4228-82b2-9ca6e7b952f7',
    issueDate: new DateTimeImmutable('2026-06-18T00:00:00+02:00'),
    currency: Currency::CZK,
    buyerCustomerParty: $buyer,
    sellerSupplierParty: $seller,
    lines: [
        new DocumentCalculatedLineInput(
            uuid: 'd4b5b29c-d658-4568-aaa9-839f11ce1446',
            lineItem: (new OutgoingQuotationLineItemInput())
                ->withCatalogueItemIdentification('A25882')
                ->addDescription(new DocumentDescription('Adam křeslo - skládačka')),
            quantity: 1.0,
            unitCode: 'Ks',
            unitPrice: 95.0,
            vatRate: 21.0,
            priceMode: DocumentPriceMode::WithoutVat,
            id: '1',
        ),
    ],
    id: 'ZAKTEST-2026-00002',
    paymentMeansCode: OutgoingQuotationPaymentMeansCode::Cash,
    payableRoundingAmount: 0.05,
);

$preview = $vario->outgoingQuotations()->previewUpsert([$quotation]);
```

The real `upsert()` write flow is also available, but it is not shown as the default example path here.

### Low-level write API

For advanced integrations, the SDK still exposes low-level write models such as:

- `IncomingOrderInput`
- `IncomingOrderLineInput`

These are useful when the integration already calculates all monetary and tax values externally and needs full control over the outgoing payload.

### Read and write model structure

Endpoint modules keep endpoint-specific read/write/API/result classes,
while shared document concepts now live in `Domain\Shared\Document`.

For example, IncomingOrder still keeps dedicated endpoint layers such as:

- `Read`
- `Write`
- `Enum`
- `Result`
- `Builder`

Generic document value objects and calculators such as descriptions,
quantities, calculated lines, tax totals, monetary totals and document
calculators are shared across document-like endpoints through:

- `Lemonade\Vario\Domain\Shared\Document\Enum`
- `Lemonade\Vario\Domain\Shared\Document\ValueObject`
- `Lemonade\Vario\Domain\Shared\Document\Write`
- `Lemonade\Vario\Domain\Shared\Document\Calculator`

Full IncomingOrder examples are available in:

```text
examples/incoming-order-query.php
examples/incoming-order-upsert-builder.php
```

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
- [IncomingOrder Domain](https://github.com/johnnyxlemonade/vario-online-sdk/wiki/IncomingOrder-Domain)
- [Filtering & Queries](https://github.com/johnnyxlemonade/vario-online-sdk/wiki/Filtering-and-Queries)
- [Logging](https://github.com/johnnyxlemonade/vario-online-sdk/wiki/Logging)
- [Token Storage](https://github.com/johnnyxlemonade/vario-online-sdk/wiki/Token-Storage)
- [Authentication](https://github.com/johnnyxlemonade/vario-online-sdk/wiki/Authentication)
- [Architecture](https://github.com/johnnyxlemonade/vario-online-sdk/wiki/Architecture)

---

## Development

Run development commands from the `sdk/` package root.

Available quality gates:

- Composer validation: `composer composer:validate`
- Platform requirements: `composer check:platform`
- PHP lint: `composer lint`
- Coding standards check: `composer cs:check`
- Coding standards fix: `composer cs:fix`
- PHPStan: `composer stan`
- PHPUnit: `composer test`
- Full QA suite: `composer qa`
- Full project check: `composer check`

Typical workflow:

``` bash
cd sdk
composer check
```

---
## Disclaimer

This project is an independent open‑source initiative and is not
affiliated with or endorsed by Vario Software.

---

## License

MIT License

Copyright (c) 2026 Honza Mudrak
