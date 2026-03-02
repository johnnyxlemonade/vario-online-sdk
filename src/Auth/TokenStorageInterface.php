<?php declare(strict_types=1);

namespace Lemonade\Vario\Auth;

interface TokenStorageInterface
{
    public function get(): ?Token;

    public function store(Token $token): void;

    public function clear(): void;
}
