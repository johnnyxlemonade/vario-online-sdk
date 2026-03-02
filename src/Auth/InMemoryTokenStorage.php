<?php declare(strict_types=1);

namespace Lemonade\Vario\Auth;

/**
 * Class InMemoryTokenStorage
 *
 * Jednoduchá in-memory implementace TokenStorageInterface.
 * Slouží pro dočasné uložení access tokenu během běhu aplikace.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Auth
 * @category    Storage
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class InMemoryTokenStorage implements TokenStorageInterface
{
    private ?Token $token = null;

    /**
     * Vrátí aktuálně uložený token.
     */
    public function get(): ?Token
    {
        if ($this->token === null) {
            return null;
        }

        if ($this->token->isExpired()) {
            $this->token = null;
            return null;
        }

        return $this->token;
    }

    /**
     * Uloží access token do paměti.
     */
    public function store(Token $token): void
    {
        $this->token = $token;
    }

    /**
     * Explicitně vymaže uložený token.
     */
    public function clear(): void
    {
        $this->token = null;
    }
}
