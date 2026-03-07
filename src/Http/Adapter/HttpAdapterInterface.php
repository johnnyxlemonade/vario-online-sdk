<?php

declare(strict_types=1);

namespace Lemonade\Vario\Http\Adapter;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Interface HttpAdapterInterface
 *
 * Adapter abstraction exposing the PSR HTTP components required
 * by the SDK transport layer.
 *
 * The adapter provides access to:
 *
 * - a PSR-18 HTTP client implementation
 * - a PSR-17 request factory
 * - a PSR-17 stream factory
 *
 * This interface decouples the SDK from any specific HTTP library
 * (e.g. Guzzle, Symfony HTTP Client) while still relying on the
 * standardized PSR contracts.
 *
 * Implementations of this adapter are typically passed to:
 *
 *     VarioApiFactory::create()
 *
 * allowing the SDK to operate with different HTTP stacks or
 * testing adapters without modifying the core client.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Http\Adapter
 * @category    Transport
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrák
 * @license     MIT
 * @since       1.0
 */
interface HttpAdapterInterface
{
    public function client(): ClientInterface;
    public function requestFactory(): RequestFactoryInterface;
    public function streamFactory(): StreamFactoryInterface;
}
