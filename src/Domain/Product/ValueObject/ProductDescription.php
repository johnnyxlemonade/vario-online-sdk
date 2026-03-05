<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\ValueObject;

/*
 * Class ProductDescription
 *
 * Product textual descriptions.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class ProductDescription
{
    public function __construct(
        private readonly ?string $shortDescription,
        private readonly ?string $description,
    ) {}

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return array{
     *     shortDescription: ?string,
     *     description: ?string
     * }
     */
    public function toArray(): array
    {
        return [
            'shortDescription' => $this->shortDescription,
            'description' => $this->description,
        ];
    }
}
