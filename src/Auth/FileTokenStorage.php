<?php

declare(strict_types=1);

namespace Lemonade\Vario\Auth;

/**
 * Class FileTokenStorage
 *
 * Stores the access token in a file on the filesystem.
 * Useful for CLI tools, cron jobs, or applications where the token
 * needs to persist between executions of the script.
 *
 * The token is stored as JSON and automatically cleared when expired.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Auth
 * @category    Storage
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class FileTokenStorage implements TokenStorageInterface
{
    private ?Token $token = null;

    public function __construct(
        private readonly string $file
    ) {}

    public function get(): ?Token
    {
        if (!is_file($this->file)) {
            return null;
        }

        $data = json_decode((string) file_get_contents($this->file), true);

        if (!is_array($data) || !isset($data['value'])) {
            return null;
        }

        $this->token = Token::fromArray($data);

        // Check if token is expired
        if ($this->token !== null && $this->token->isExpired()) {
            $this->clear();
            return null;
        }

        return $this->token;
    }

    public function store(Token $token): void
    {
        $this->token = $token;

        file_put_contents(
            $this->file,
            json_encode($token->toArray(), JSON_THROW_ON_ERROR)
        );
    }

    public function clear(): void
    {
        $this->token = null;

        if (is_file($this->file)) {
            unlink($this->file);
        }
    }

}
