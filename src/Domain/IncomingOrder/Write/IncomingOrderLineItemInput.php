<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Write;

use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderDescription;
use Lemonade\Vario\Domain\IncomingOrder\ValueObject\IncomingOrderUnitConversionFactor;

/**
 * Class IncomingOrderLineItemInput
 *
 * Product identification and descriptive data for an IncomingOrder line.
 *
 * At least one of catalogue, seller or standard identification should be
 * provided when the line represents a product linked to catalog data.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderLineItemInput
{
    /** @var list<IncomingOrderDescription> */
    private array $descriptions = [];

    /** @var list<IncomingOrderAdditionalAttributeInput> */
    private array $additionalAttributes = [];

    /** @var list<IncomingOrderUnitConversionFactor> */
    private array $unitConversionFactors = [];

    public function __construct(
        private ?string $buyersItemIdentification = null,
        private ?string $catalogueItemIdentification = null,
        private ?string $sellersItemIdentification = null,
        private ?string $standardItemIdentification = null,
    ) {}

    public function getBuyersItemIdentification(): ?string
    {
        return $this->buyersItemIdentification;
    }

    public function withBuyersItemIdentification(?string $buyersItemIdentification): self
    {
        $this->buyersItemIdentification = $buyersItemIdentification;

        return $this;
    }

    public function getCatalogueItemIdentification(): ?string
    {
        return $this->catalogueItemIdentification;
    }

    public function withCatalogueItemIdentification(?string $catalogueItemIdentification): self
    {
        $this->catalogueItemIdentification = $catalogueItemIdentification;

        return $this;
    }

    public function getSellersItemIdentification(): ?string
    {
        return $this->sellersItemIdentification;
    }

    public function withSellersItemIdentification(?string $sellersItemIdentification): self
    {
        $this->sellersItemIdentification = $sellersItemIdentification;

        return $this;
    }

    public function getStandardItemIdentification(): ?string
    {
        return $this->standardItemIdentification;
    }

    public function withStandardItemIdentification(?string $standardItemIdentification): self
    {
        $this->standardItemIdentification = $standardItemIdentification;

        return $this;
    }

    /**
     * @return list<IncomingOrderDescription>
     */
    public function getDescriptions(): array
    {
        return $this->descriptions;
    }

    public function addDescription(IncomingOrderDescription $description): self
    {
        $this->descriptions[] = $description;

        return $this;
    }

    /**
     * @param list<IncomingOrderDescription> $descriptions
     */
    public function withDescriptions(array $descriptions): self
    {
        $this->descriptions = $descriptions;

        return $this;
    }

    /**
     * @return list<IncomingOrderAdditionalAttributeInput>
     */
    public function getAdditionalAttributes(): array
    {
        return $this->additionalAttributes;
    }

    public function addAdditionalAttribute(IncomingOrderAdditionalAttributeInput $attribute): self
    {
        $this->additionalAttributes[] = $attribute;

        return $this;
    }

    /**
     * @param list<IncomingOrderAdditionalAttributeInput> $additionalAttributes
     */
    public function withAdditionalAttributes(array $additionalAttributes): self
    {
        $this->additionalAttributes = $additionalAttributes;

        return $this;
    }

    /**
     * @return list<IncomingOrderUnitConversionFactor>
     */
    public function getUnitConversionFactors(): array
    {
        return $this->unitConversionFactors;
    }

    public function addUnitConversionFactor(IncomingOrderUnitConversionFactor $unitConversionFactor): self
    {
        $this->unitConversionFactors[] = $unitConversionFactor;

        return $this;
    }

    /**
     * @param list<IncomingOrderUnitConversionFactor> $unitConversionFactors
     */
    public function withUnitConversionFactors(array $unitConversionFactors): self
    {
        $this->unitConversionFactors = $unitConversionFactors;

        return $this;
    }

    public function hasAnyProductIdentification(): bool
    {
        return $this->catalogueItemIdentification !== null
            || $this->sellersItemIdentification !== null
            || $this->standardItemIdentification !== null;
    }
}
