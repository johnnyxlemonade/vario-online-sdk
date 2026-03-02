<?php declare(strict_types=1);

namespace Lemonade\Vario\Http\Adapter;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

interface HttpAdapterInterface
{
    public function client(): ClientInterface;
    public function requestFactory(): RequestFactoryInterface;
    public function streamFactory(): StreamFactoryInterface;
}
