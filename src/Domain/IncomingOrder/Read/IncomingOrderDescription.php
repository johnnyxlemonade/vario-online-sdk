<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Read;

/**
 * Class DocumentDescription
 *
 * Text description entry for IncomingOrder line item.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class DocumentDescription
{
    public function __construct(
        private readonly string $text,
        private readonly ?string $langId = null,
    ) {}

    public function getText(): string
    {
        return $this->text;
    }

    public function getLangId(): ?string
    {
        return $this->langId;
    }
}
