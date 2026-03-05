<?php

declare(strict_types=1);

namespace Lemonade\Vario\Exception;

class ApiException extends VarioException
{
    public function __construct(
        string $message,
        private readonly ?int $statusCode = null,
        private readonly ?string $responseBody = null,
        ?\Throwable $previous = null
    ) {
        // HTTP status je transport metadata,
        // nikoliv runtime exception code.
        parent::__construct($message, 0, $previous);
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getResponseBody(): ?string
    {
        return $this->responseBody;
    }
}
