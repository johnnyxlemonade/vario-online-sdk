<?php

declare(strict_types=1);

namespace Lemonade\Vario\Client\Http;

use Lemonade\Vario\Auth\Storage\TokenStorageInterface;
use Psr\Http\Message\RequestInterface;

final class RequestAuthenticator
{
    public function authenticate(
        RequestInterface $request,
        TokenStorageInterface $tokenStorage
    ): RequestInterface {

        $request = $request->withHeader(
            'X-Requested-With',
            'XMLHttpRequest'
        );

        if ($request->hasHeader('Authorization')) {
            $request = $request->withoutHeader('Authorization');
        }

        $token = $tokenStorage->get();

        if ($token === null) {
            return $request;
        }

        return $request->withHeader(
            'Authorization',
            'Bearer ' . $token->value
        );
    }
}
