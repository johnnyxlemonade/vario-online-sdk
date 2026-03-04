# Vario Online PHP SDK

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg)](https://www.php.net/)
[![Packagist Version](https://img.shields.io/packagist/v/lemonade/vario-online-sdk)](https://packagist.org/packages/lemonade/vario-online-sdk)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-level%20max-brightgreen.svg)](https://phpstan.org/)
[![Static Analysis](https://github.com/johnnyxlemonade/vario-online-sdk/actions/workflows/phpstan.yml/badge.svg)](https://github.com/johnnyxlemonade/vario-online-sdk/actions/workflows/phpstan.yml)
[![PHPUnit](https://github.com/johnnyxlemonade/vario-online-sdk/actions/workflows/phpunit.yml/badge.svg)](https://github.com/johnnyxlemonade/vario-online-sdk/actions/workflows/phpunit.yml)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Typed, PSR-compatible PHP SDK for integrating with **Vario Online API**.

A modern, strongly‑typed SDK designed for PHP 8.1+ applications.
The SDK exposes a stable **domain-oriented API**, not just a transport wrapper.

It provides clean abstractions over Vario endpoints, dataset views and paged queries, including automatic authentication, retry handling and a fully replaceable HTTP layer.

------------------------------------------------------------------------

## Features

-   Typed API facade (`VarioApi`)
-   Domain-driven read models (KnownParty, PostalAddress,
    Identification)
-   Immutable value objects
-   Automatic authentication & token refresh
-   PSR‑18 HTTP client support
-   Replaceable HTTP adapter (no vendor lock‑in)
-   Lazy‑loaded API modules
-   Explicit anti‑corruption mapping layer
-   Unified exception model
-   PHPStan **level max** ready (strict typing)

------------------------------------------------------------------------

## Requirements

- PHP 8.1+
- Composer
- Access to Vario Online API (VPN + credentials)
- A PSR‑18 HTTP client implementation

------------------------------------------------------------------------

## Installation
Install the SDK via Composer:

```bash
composer require lemonade/vario-online-sdk
```

The SDK itself does **not ship with a built-in HTTP client**.

Instead, it follows the **PSR-18 HTTP Client standard**, which allows you to use any compatible HTTP client implementation. This keeps the SDK lightweight and avoids vendor lock-in.

To use the SDK you must install:

 - a **PSR-18 HTTP client**
 - a **PSR-7 / PSR-17 implementation**

Example using **Guzzle**:

```bash
composer require guzzlehttp/guzzle guzzlehttp/psr7
```

You can also use other implementations such as:
- `symfony/http-client`
- `nyholm/psr7`
- `laminas/laminas-diactoros`

Any PSR-18 compatible client can be used with the SDK.

------------------------------------------------------------------------

## Quick Start

``` php
use Lemonade\Vario\VarioApiFactory;
use Lemonade\Vario\VarioClientConfig;
use Lemonade\Vario\ValueObject\DatasetViewQuery;
use Lemonade\Vario\ValueObject\CustomDatasetView;
use Lemonade\Vario\Http\Adapter\GuzzleHttpAdapter;

$config = new VarioClientConfig(
    baseUrl: 'https://your-vario-server',
    loginName: 'USER',
    password: 'PASSWORD',
    companyNumber: 'COMPANY'
);

$vario = VarioApiFactory::create(
    $config,
    new GuzzleHttpAdapter($config)
);

$query = DatasetViewQuery::for(
    new CustomDatasetView('Katalog/KatalogCenikAPI'),
    pageLength: 100
);

$data = $vario->datasetView()->get($query);
```

------------------------------------------------------------------------

## Domain Example --- Known Parties

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

-   `getDisplayAddress()`
-   `getCityLine()` / `getStreetLine()`
-   `getCompanyNumber()`
-   `getVatId()`
-   `getIdentificationsOrEmpty()`

No manual array parsing required.

Entities provide semantic helpers:

- `getDisplayAddress()`
- `getCityLine()` / `getStreetLine()`
- `getCompanyNumber()`
- `getVatId()`
- `getIdentificationsOrEmpty()`

No manual array parsing required.

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
        QueryFilters::equals('ElectronicMail', 'email@adress.com')
    );

$items = $vario->knownParties()->query($query);
```

**Note:**\
Filtering is limited to properties exposed by the Vario API endpoint.
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
    $config,
    new GuzzleHttpAdapter($config)
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

## Authentication Lifecycle

Authentication is handled automatically:

1. Initial authentication during factory creation (fail‑fast).
2. Access token stored internally.
3. When API returns **401**, the client:
    - re‑authenticates
    - retries the request once.
4. Second failure throws `AuthenticationException`.

No manual token handling is required.

------------------------------------------------------------------------

## Error Handling

All SDK exceptions extend:

    VarioException

Hierarchy:

    VarioException
    ├── ConfigurationException
    ├── AuthenticationException
    └── ApiException
         └── ForbiddenException

------------------------------------------------------------------------

## Architecture

Layered (ports & adapters + domain mapping):

    VarioApi (Facade)
            ↓
    API Modules
            ↓
    Mapper (anti‑corruption layer)
            ↓
    Domain Models
            ↓
    VarioClient (transport + retry + auth)
            ↓
    HttpAdapterInterface (PSR boundary)
            ↓
    HTTP implementation (Guzzle / Symfony / custom)

------------------------------------------------------------------------

## Development

Run static analysis:

```bash
composer stan
```

Run tests:

```bash
composer test
```

Run full CI locally:

```bash
composer ci
```

------------------------------------------------------------------------

## License

MIT License

Copyright (c) 2026 Jan Mudrák

See the LICENSE file for details.
