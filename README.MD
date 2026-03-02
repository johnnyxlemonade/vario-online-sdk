# Vario Online PHP SDK

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg)](https://www.php.net/)
[![Packagist Version](https://img.shields.io/packagist/v/lemonade/vario-online-sdk)](https://packagist.org/packages/lemonade/vario-online-sdk)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg)](https://phpstan.org/)
[![Static Analysis](https://github.com/johnnyxlemonade/vario-online-sdk/actions/workflows/phpstan.yml/badge.svg)](https://github.com/johnnyxlemonade/vario-online-sdk/actions)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Typed, PSR-compatible PHP SDK for integrating with **Vario Online API**.

A modern, strongly-typed SDK designed for PHP 8.1+ applications.  
Provides a clean abstraction over Vario endpoints, dataset views and paged queries,
including automatic authentication, retry handling and a fully replaceable HTTP layer.

---

## Features

- Typed API facade
- DatasetView abstraction (enum + custom views)
- Immutable query objects
- Automatic authentication & token refresh
- PSR‑18 HTTP client support
- Replaceable HTTP adapter (no vendor lock‑in)
- Unified exception model
- Lazy‑loaded API modules
- Environment‑based configuration
- PHPStan level 9 + strict rules ready

---

## Requirements

- PHP 8.1+
- Composer
- Access to Vario Online API (VPN + credentials)
- A PSR‑18 HTTP client implementation

---

## Installation

Install the SDK:

```bash
composer require lemonade/vario-online-client
```

The SDK is transport‑agnostic and **requires a HTTP adapter**.

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

---

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

```
VARIO_URL
VARIO_LOGIN
VARIO_PASSWORD
VARIO_COMPANY
```

---

## Authentication Lifecycle

Authentication is handled automatically:

1. Initial authentication during factory creation (fail‑fast).
2. Access token stored internally.
3. When API returns **401**, the client:
    - re‑authenticates
    - retries the request once.
4. Second failure throws `AuthenticationException`.

No manual token handling is required.

---

## Error Handling

All SDK exceptions extend:

```
VarioException
```

Hierarchy:

```
VarioException
├── ConfigurationException
├── AuthenticationException
└── ApiException
     └── ForbiddenException
```

Example:

```php
use Lemonade\Vario\Exception\ConfigurationException;
use Lemonade\Vario\Exception\AuthenticationException;
use Lemonade\Vario\Exception\ApiException;
use Lemonade\Vario\Exception\VarioException;

try {
    $data = $vario->datasetView()->get($query);
}
catch (ConfigurationException $e) {}
catch (AuthenticationException $e) {}
catch (ApiException $e) {}
catch (VarioException $e) {}
```

---

## Architecture

The SDK follows a layered (ports & adapters) architecture:

```
VarioApi (Facade)
        ↓
API Modules
        ↓
VarioClient (transport + retry + auth)
        ↓
HttpAdapterInterface (PSR boundary)
        ↓
HTTP implementation (Guzzle / Symfony / custom)
```

---

## Development

Run tests:

```bash
composer test
```

Run static analysis:

```bash
composer stan
```

---

## License

MIT License

Copyright (c) 2026 Jan Mudrák

See the LICENSE file for details.
