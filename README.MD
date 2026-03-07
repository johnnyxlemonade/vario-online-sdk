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
[https://vario.cz/](https://vario.cz/)

The SDK exposes a stable **domain-oriented API**, not just a transport
wrapper. It provides clean abstractions over Vario endpoints, dataset
views and paged queries, including automatic authentication, retry
handling and a fully replaceable HTTP layer.

---

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


---

## Features

-   Typed API facade (`VarioApi`)
-   Domain-driven read models (KnownParty, Product)
-   Immutable value objects
-   DatasetView → Domain mapping layer
-   Automatic authentication & token refresh
-   PSR‑18 HTTP client support
-   PSR‑3 logging support
-   Structured request/response logging with duration metrics
-   Replaceable HTTP adapter (no vendor lock‑in)
-   Configurable token storage (memory, session, custom)
-   Lazy‑loaded API modules
-   Unified exception model
-   PHPStan **level max** ready

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

## License

MIT License

Copyright (c) 2026 Jan Mudrák
