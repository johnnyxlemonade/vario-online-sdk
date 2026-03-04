<?php declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\ValueObject;

/**
 * Class ProductIdentity
 *
 * Represents the core identity of a product in Vario ERP.
 *
 * Contains the stable technical identifier (UUID) together with
 * business identifiers used in catalogues and integrations.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class ProductIdentity
{
    public function __construct(
        private readonly string $uuid,
        private readonly ?string $sku,
        private readonly ?string $catalogNumber,
        private readonly ?string $name,
    ) {}

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function getCatalogNumber(): ?string
    {
        return $this->catalogNumber;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return array{
     *     uuid: string,
     *     sku: ?string,
     *     catalogNumber: ?string,
     *     name: ?string
     * }
     */
    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'sku' => $this->sku,
            'catalogNumber' => $this->catalogNumber,
            'name' => $this->name,
        ];
    }
}
