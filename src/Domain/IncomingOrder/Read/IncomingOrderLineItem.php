<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Read;

use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentDescription;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentUnitConversionFactor;

/**
 * Class IncomingOrderLineItem
 *
 * Immutable domain read model representing item identification
 * and descriptive metadata of a single IncomingOrder line.
 *
 * Instances of this class are created by IncomingOrderMapper when
 * converting raw Vario API payloads into strongly-typed domain objects.
 *
 * The model intentionally exposes stable product identification fields
 * and lightweight descriptive structures needed by integrations.
 *
 * Any additional fields returned by the API are preserved in the
 * `$extra` payload to maintain forward compatibility.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderLineItem
{
    /**
     * @param list<DocumentDescription> $descriptions
     * @param list<IncomingOrderAdditionalAttribute> $additionalAttributes
     * @param list<IncomingOrderTextualAttribute> $textualAttributes
     * @param list<IncomingOrderNumericAttribute> $numericAttributes
     * @param list<DocumentUnitConversionFactor> $unitConversionFactors
     * @param array<string,mixed> $extra Additional unmapped API fields.
     */
    public function __construct(
        // =========================
        // PRODUCT IDENTIFICATION
        // =========================
        private readonly ?string $buyersItemIdentification = null,
        private readonly ?string $catalogueItemIdentification = null,
        private readonly ?string $sellersItemIdentification = null,
        private readonly ?string $standardItemIdentification = null,

        // =========================
        // DESCRIPTIVE DATA
        // =========================
        private readonly array $descriptions = [],
        private readonly array $additionalAttributes = [],
        private readonly array $textualAttributes = [],
        private readonly array $numericAttributes = [],
        private readonly array $unitConversionFactors = [],

        // =========================
        // FORWARD COMPATIBILITY
        // =========================
        private readonly array $extra = [],
    ) {}

    public function getBuyersItemIdentification(): ?string
    {
        return $this->buyersItemIdentification;
    }

    public function getCatalogueItemIdentification(): ?string
    {
        return $this->catalogueItemIdentification;
    }

    public function getSellersItemIdentification(): ?string
    {
        return $this->sellersItemIdentification;
    }

    public function getStandardItemIdentification(): ?string
    {
        return $this->standardItemIdentification;
    }

    public function hasDescriptions(): bool
    {
        return $this->descriptions !== [];
    }

    /**
     * @return list<DocumentDescription>
     */
    public function getDescriptions(): array
    {
        return $this->descriptions;
    }

    public function hasAdditionalAttributes(): bool
    {
        return $this->additionalAttributes !== [];
    }

    /**
     * @return list<IncomingOrderAdditionalAttribute>
     */
    public function getAdditionalAttributes(): array
    {
        return $this->additionalAttributes;
    }

    /**
     * @return list<IncomingOrderTextualAttribute>
     */
    public function getTextualAttributes(): array
    {
        return $this->textualAttributes;
    }

    /**
     * @return list<IncomingOrderNumericAttribute>
     */
    public function getNumericAttributes(): array
    {
        return $this->numericAttributes;
    }

    public function hasUnitConversionFactors(): bool
    {
        return $this->unitConversionFactors !== [];
    }

    /**
     * @return list<DocumentUnitConversionFactor>
     */
    public function getUnitConversionFactors(): array
    {
        return $this->unitConversionFactors;
    }

    public function hasAnyProductIdentification(): bool
    {
        return $this->catalogueItemIdentification !== null
            || $this->sellersItemIdentification !== null
            || $this->standardItemIdentification !== null
            || $this->buyersItemIdentification !== null;
    }

    public function getPrimaryDescription(): ?DocumentDescription
    {
        return $this->descriptions[0] ?? null;
    }

    public function getPrimaryDescriptionText(): ?string
    {
        return $this->getPrimaryDescription()?->getText();
    }

    public function hasTextualAttributes(): bool
    {
        return $this->textualAttributes !== [];
    }

    public function hasNumericAttributes(): bool
    {
        return $this->numericAttributes !== [];
    }

    /**
     * Returns additional API fields not mapped to explicit properties.
     *
     * @return array<string,mixed>
     */
    public function getExtra(): array
    {
        return $this->extra;
    }
}
