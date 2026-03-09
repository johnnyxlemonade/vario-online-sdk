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
- **Automatic authentication & token refresh**
- **PSR-18 HTTP client support**
- PSR-3 logging support
- Structured request/response logging with duration metrics
- **Replaceable HTTP adapter** (no vendor lock-in)
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
The SDK is composed of several layers separating application usage, 
API modules, domain mapping and transport infrastructure.

```
Application
  │
  ▼
VarioApiFactory
  │
  ├─ VarioClientConfig
  ├─ HttpAdapterInterface
  └─ TokenStorageInterface
  │
  ▼
Authenticator
  │
  ▼
VarioClient
  (auth + retry + request handling)
  │
  ▼
VarioApi (Facade)
  │
  ▼
API Modules
  (KnownPartyApi, DatasetViewApi, IncomingOrderApi…)
  │
  ▼
Mapper / Normalizer Layer
  │
  ▼
Domain Models
  (KnownParty, Product, ValueObjects)
  │
  ▼
HttpAdapterInterface
  (PSR-18 boundary)
  │
  ▼
HTTP Client
  (Guzzle / Symfony / custom)

```

This architecture keeps the domain layer independent from the HTTP
transport and allows integrations to replace the underlying HTTP
client without modifying application logic.

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
