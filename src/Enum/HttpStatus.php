<?php

declare(strict_types=1);

namespace Lemonade\Vario\Enum;

enum HttpStatus: int
{
    case OK = 200;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case TOO_MANY_REQUESTS = 429;
    case INTERNAL_SERVER_ERROR = 500;

    public static function isClientErrorCode(int $status): bool
    {
        return $status >= 400 && $status < 500;
    }

    public static function isServerErrorCode(int $status): bool
    {
        return $status >= 500;
    }
}
