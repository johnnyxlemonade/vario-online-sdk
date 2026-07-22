# Vario Online API PHP SDK (Community Vario ERP Integration Library)

[![PHP Version](https://img.shields.io/packagist/php-v/lemonade/vario-online-sdk)](https://packagist.org/packages/lemonade/vario-online-sdk)
[![Packagist Version](https://img.shields.io/packagist/v/lemonade/vario-online-sdk)](https://packagist.org/packages/lemonade/vario-online-sdk)
[![Downloads](https://img.shields.io/packagist/dt/lemonade/vario-online-sdk)](https://packagist.org/packages/lemonade/vario-online-sdk)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%2010-brightgreen.svg)](https://phpstan.org/)
[![Tests](https://github.com/johnnyxlemonade/vario-online-sdk/actions/workflows/phpunit.yml/badge.svg)](https://github.com/johnnyxlemonade/vario-online-sdk/actions/workflows/phpunit.yml)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

A strongly typed PHP SDK for the Vario Online API, designed for building custom integrations with Vario ERP systems.

This project is an independent community-maintained client library for developers who want to integrate PHP applications with the Vario ERP platform. It is not officially affiliated with Vario Software.

The SDK provides a modern domain-oriented abstraction layer over the Vario Online API including dataset queries, authentication handling, domain mapping, and transport-agnostic HTTP communication.

It is designed for building stable ERP integrations where API payload structures may vary between installations.

More information about Vario ERP:
https://vario.cz/

---

## Typical Use Cases

This SDK is commonly used for:

- integrating Vario ERP with e-commerce platforms
- synchronizing products, customers, and orders
- building data pipelines from Vario datasets
- exporting ERP data into BI or analytics systems
- creating custom ERP dashboards or integrations
- automating ERP workflows

---

## Why this SDK exists

This SDK exists to provide a more structured integration layer for PHP applications that work with Vario dataset rows and endpoint payloads.

At the transport boundary, integrations often still need to deal with low-level request payloads, decoded response arrays, and mapping decisions between ERP data and application code.

This SDK adds a domain-oriented layer on top of that workflow.

Instead of keeping those concerns spread across application code, developers can work with typed domain models such as:

- `KnownParty`
- `Product`
- `IncomingOrder`

These models are backed by immutable value objects, normalizers, mappers, and a shared document foundation for document-like endpoints.

For write flows, document-oriented modules such as `IncomingOrder` and `OutgoingQuotation` share common primitives for calculated lines, price mode, descriptions, quantities, tax totals, monetary totals, tax exchange rates, and line/total calculators.

Goals of the SDK:

- stable integration surface
- strong typing and IDE support
- separation between API payloads and application logic
- maintainable ERP integrations

---

## Features

- Typed API facade (`VarioApi`)
- Domain-driven read models (`KnownParty`, `Product`)
- Immutable value objects
- DatasetView -> Domain mapping layer
- Streaming dataset processing (`iterate()`, lazy pipelines)
- IncomingOrder builder with shared document calculated lines and automatic line/total calculation
- OutgoingQuotation builder/write flow with payload preview and upsert support
- Shared document primitives and calculators for document-like endpoints (`Domain\Shared\Document`)
- Automatic authentication and token refresh
- PSR-18 transport layer (replaceable HTTP client, no vendor lock-in)
- PSR-3 logging support
- Structured request/response logging with duration metrics
- Configurable token storage (memory, session, custom)
- Lazy-loaded API modules
- Unified exception model
- PHPStan level 10 compliant

---

## Supported Vario API Areas

The SDK provides abstractions for several Vario Online API modules:

- DatasetView API
- KnownParty API
- Product catalog datasets
- Incoming orders: query, mapping, payload preview, builder-based upsert flow
- Outgoing quotations: payload preview, builder-based upsert flow

Additional integrations can be built through the generic DatasetView layer.

---

## Architecture Overview

The SDK uses a layered architecture that separates application usage, API modules, domain mapping, and transport infrastructure.

Main goals:

- keep application code independent from transport details
- provide typed domain models instead of raw API arrays
- isolate API schema volatility behind a mapping layer
- allow replaceable HTTP clients through PSR-18

```text
Application
  |
  v
VarioApiFactory
  |
  +- VarioClientConfig
  +- TokenStorageInterface
  +- PSR-18 Client Discovery
  |
  v
VarioApi (Facade)
  |
  v
API Modules
  |
  +- Dataset APIs (Queries)
  |    v
  |  Mapper / Normalizer Layer
  |    v
  |  Domain Read Models (Product, KnownParty, ...)
  |
  +- Endpoint APIs (Actions)
       v
     Domain Entities / Responses
  |
  v
VarioClient (Transport Orchestrator)
  |
  +- RequestAuthenticator
  +- RequestLogger
  +- ResponseHandler
  |
  v
PSR-18 HTTP Client Interface
  |
  v
External HTTP Client
(Guzzle / Symfony HttpClient / Nyholm PSR-7 / Laminas Diactoros / custom)
```

Applications interact with the SDK through the `VarioApi` facade created by the `VarioApiFactory`. The factory assembles the complete client stack including configuration, token storage, and HTTP transport.

### API Modules

The SDK exposes functionality through API modules, each responsible for a specific area of the Vario API.

Two integration styles are supported:

**Dataset APIs (Queries)**  
These APIs retrieve data from Vario datasets. The responses are normalized and mapped into domain read models. This layer isolates application logic from dataset column naming and schema changes.

**Endpoint APIs (Actions)**  
These APIs interact with specific Vario endpoints such as document creation or entity updates. They typically return domain entities or response objects directly without passing through the dataset mapping layer.

### Shared document model

Document-like endpoints share a common foundation under `Lemonade\Vario\Domain\Shared\Document`.

This shared layer includes:

- `DocumentCalculatedLineInput`
- `DocumentPriceMode`
- `DocumentDescription`
- `DocumentQuantity`
- `DocumentMonetaryTotal`
- `DocumentTaxTotal`
- `DocumentTaxExchangeRate`
- line and totals calculators

Endpoint-specific root models remain separate where they represent actual endpoint semantics:

- `IncomingOrderInput`
- `OutgoingQuotationInput`
- endpoint-specific API modules
- endpoint-specific result objects

### Mapping Layer

For dataset-based integrations the SDK introduces a mapping and normalization layer.

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

The transport boundary follows the PSR-18 HTTP Client standard, allowing developers to use any compatible HTTP client implementation such as Guzzle or Symfony HttpClient.

This design keeps transport concerns out of domain logic.

---

## Strategic Design Decisions (ADR)

The development of this SDK was guided by several architectural principles.

### 1. Zero-Mixed Policy (PHPStan Level 10)

The library enforces Level 10 static analysis with strict rules. API responses are mapped through strict array shapes so structural problems are caught during analysis instead of at runtime.

### 2. Memory-First Streaming and Lazy Pipelines

Vario datasets can contain tens of thousands of records. The SDK implements streaming generators (`iterate()`) and lazy pipelines so large catalogs can be processed with minimal memory overhead.

### 3. Domain-Oriented Mapping Layer

The Vario API schema can vary between installations. The mapping layer keeps application code coupled to stable immutable domain models while the SDK handles translation from backend fields.

### 4. Transport Agnostic (PSR-18)

By following PSR-18 and PSR-17, the SDK avoids vendor lock-in. Transport-level concerns remain isolated from domain logic.

---

## Roadmap

The SDK will continue evolving with improvements focused on extensibility, performance, and maintainability.

### Middleware Pipeline

Introduce a request/response middleware pipeline to reduce responsibilities currently handled inside `VarioClient`.

Planned processing flow:

```text
Request
  |
  v
AuthenticationMiddleware
  |
  v
LoggingMiddleware
  |
  v
RetryMiddleware
  |
  v
HTTP Client
  |
  v
ResponseMiddleware
  |
  v
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

- PHP 8.1+
- Composer
- Access to Vario Online API (VPN + credentials)
- PSR-18 HTTP client implementation
- PSR-3 compatible logger

---

## Installation

Install the SDK via Composer:

```bash
composer require lemonade/vario-online-sdk
```

The SDK does not ship with a built-in HTTP client.

Instead, it follows the PSR-18 HTTP Client standard, which allows you to use any compatible HTTP client implementation.

Example using Guzzle:

```bash
composer require guzzlehttp/guzzle guzzlehttp/psr7
```

---

## Quick Start

```php
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

## Domain Example - Incoming Orders

The SDK provides a typed `IncomingOrder` module for both reading and writing incoming order documents.

For write operations, the recommended entry point is `IncomingOrderBuilder`.

It allows developers to define order lines using a simplified high-level input and automatically derives:

- line totals without VAT
- line totals with VAT
- tax subtotals
- document monetary total
- document tax total

### Recommended write flow

```php
use DateTimeImmutable;
use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\IncomingOrder\Builder\IncomingOrderBuildInput;
use Lemonade\Vario\Domain\IncomingOrder\Builder\IncomingOrderBuilder;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPaymentMeansCode;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineItemInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderPartiesInput;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentPriceMode;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentIdentityInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineIdentityInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLinePriceInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineQuantityInput;

$builder = new IncomingOrderBuilder();

$lineItem = (new IncomingOrderLineItemInput())
    ->withCatalogueItemIdentification('SKU-001')
    ->withSellersItemIdentification('SKU-001');

$order = $builder->build(new IncomingOrderBuildInput(
    identity: new DocumentIdentityInput(
        uuid: 'e4daf94d-fd98-4f7d-a7c6-93cd21dee5f8',
    ),
    issueDate: new DateTimeImmutable('2024-04-02T00:00:00+02:00'),
    currency: Currency::CZK,
    parties: new IncomingOrderPartiesInput(
        buyerCustomerParty: $buyer,
        sellerSupplierParty: $seller,
    ),
    lines: [
        new DocumentCalculatedLineInput(
            identity: new DocumentCalculatedLineIdentityInput(
                uuid: 'd2045e34-49b4-4238-84e2-950362f2007e',
            ),
            lineItem: $lineItem,
            quantity: new DocumentCalculatedLineQuantityInput(
                value: 2.0,
                unitCode: 'Ks',
            ),
            price: new DocumentCalculatedLinePriceInput(
                unitPrice: 100.0,
                vatRate: 21.0,
                priceMode: DocumentPriceMode::WithoutVat,
                lineAllowanceAmount: 20.0,
            ),
        ),
    ],
    paymentMeansCode: IncomingOrderPaymentMeansCode::BankAccount,
));

$preview = $vario->incomingOrders()->previewUpsert([$order]);
```

`unitPrice` is the final unit price without VAT after discount, `lineAllowanceAmount` is the discount without VAT, and Vario derives the base price before discount as `LineExtensionAmount + LineAllowanceAmount`. `LineAllowanceAmount` does not change SDK totals.

### Low-level write API

For advanced integrations, the SDK still exposes low-level write models such as:

- `IncomingOrderInput`
- `IncomingOrderLineInput`

These are useful when the integration already calculates all monetary and tax values externally and needs full control over the outgoing payload.

## Domain Example - Outgoing Quotations

`OutgoingQuotationBuilder` uses the same shared document model. You define high-level calculated lines, the builder derives line amounts, monetary totals, and tax totals, and `previewUpsert()` returns the exact payload.

```php
use DateTimeImmutable;
use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Builder\OutgoingQuotationBuildInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Builder\OutgoingQuotationBuilder;
use Lemonade\Vario\Domain\OutgoingQuotation\Enum\OutgoingQuotationPaymentMeansCode;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationLineItemInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationPartiesInput;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentPriceMode;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentDescription;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentIdentityInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineIdentityInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLinePriceInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineQuantityInput;
use Lemonade\Vario\Domain\Shared\Identification;
use Lemonade\Vario\Domain\Shared\IdentificationScheme;

$buyer = (new KnownPartyInput('A - Storex, v.o.s.'))
    ->addIdentification(new Identification(IdentificationScheme::UIN, '620927153', 'CZ'));

$seller = (new KnownPartyInput(''))
    ->addIdentification(new Identification(IdentificationScheme::VAT, 'CZ61681229', 'CZ'));

$quotation = (new OutgoingQuotationBuilder())->build(new OutgoingQuotationBuildInput(
    identity: new DocumentIdentityInput(
        uuid: 'c676048c-3789-4228-82b2-9ca6e7b952f7',
        id: 'ZAKTEST-2026-00002',
    ),
    issueDate: new DateTimeImmutable('2026-06-18T00:00:00+02:00'),
    currency: Currency::CZK,
    parties: new OutgoingQuotationPartiesInput(
        buyerCustomerParty: $buyer,
        sellerSupplierParty: $seller,
    ),
    lines: [
        new DocumentCalculatedLineInput(
            identity: new DocumentCalculatedLineIdentityInput(
                uuid: 'd4b5b29c-d658-4568-aaa9-839f11ce1446',
                id: '1',
            ),
            lineItem: (new OutgoingQuotationLineItemInput())
                ->withCatalogueItemIdentification('A25882')
                ->addDescription(new DocumentDescription('Adam kreslo - skladacka')),
            quantity: new DocumentCalculatedLineQuantityInput(
                value: 1.0,
                unitCode: 'Ks',
            ),
            price: new DocumentCalculatedLinePriceInput(
                unitPrice: 95.0,
                vatRate: 21.0,
                priceMode: DocumentPriceMode::WithoutVat,
                lineAllowanceAmount: 25.0,
            ),
        ),
    ],
    paymentMeansCode: OutgoingQuotationPaymentMeansCode::Cash,
    payableRoundingAmount: 0.05,
));

$preview = $vario->outgoingQuotations()->previewUpsert([$quotation]);
```

`unitPrice` is the final unit price without VAT after discount, `lineAllowanceAmount` is the discount without VAT, and Vario derives the base price before discount as `LineExtensionAmount + LineAllowanceAmount`. `LineAllowanceAmount` does not change SDK totals.

The real `upsert()` write flow is also available, but it is not shown as the default example path here.

### Read and write model structure

Endpoint modules keep endpoint-specific read/write/API/result classes, while shared document concepts now live in `Domain\Shared\Document`.

For example, IncomingOrder still keeps dedicated endpoint layers such as:

- `Read`
- `Write`
- `Enum`
- `Result`
- `Builder`

Generic document value objects and calculators such as descriptions, quantities, calculated lines, tax totals, monetary totals, and document calculators are shared across document-like endpoints through:

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

Run development commands from the SDK repository root.

Common checks:

- Composer validation: `composer validate --strict`
- PHP lint: `composer lint`
- Coding standards check: `composer cs:check`
- PHPStan: `composer stan`
- PHPUnit: `composer test`
- Full project check: `composer check`

Code style fixes:

- `composer cs:fix`

Additional Composer scripts are defined in `composer.json`.

```bash
composer check
```

---

## Disclaimer

This project is an independent open-source initiative and is not affiliated with or endorsed by Vario Software.

---

## License

MIT License

Copyright (c) 2026 Jan Mudrak
