<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\OutgoingQuotation\Write;

use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentDescription;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineItemInputInterface;

final class OutgoingQuotationLineItemInput implements DocumentCalculatedLineItemInputInterface
{
    /**
     * @param list<DocumentDescription> $descriptions
     */
    public function __construct(
        private readonly ?string $buyersItemIdentification = null,
        private readonly ?string $catalogueItemIdentification = null,
        private readonly ?string $sellersItemIdentification = null,
        private readonly ?string $standardItemIdentification = null,
        private readonly array $descriptions = [],
    ) {}

    public function getBuyersItemIdentification(): ?string
    {
        return $this->buyersItemIdentification;
    }

    public function withBuyersItemIdentification(?string $buyersItemIdentification): self
    {
        return new self(
            buyersItemIdentification: $buyersItemIdentification,
            catalogueItemIdentification: $this->catalogueItemIdentification,
            sellersItemIdentification: $this->sellersItemIdentification,
            standardItemIdentification: $this->standardItemIdentification,
            descriptions: $this->descriptions,
        );
    }

    public function getCatalogueItemIdentification(): ?string
    {
        return $this->catalogueItemIdentification;
    }

    public function withCatalogueItemIdentification(?string $catalogueItemIdentification): self
    {
        return new self(
            buyersItemIdentification: $this->buyersItemIdentification,
            catalogueItemIdentification: $catalogueItemIdentification,
            sellersItemIdentification: $this->sellersItemIdentification,
            standardItemIdentification: $this->standardItemIdentification,
            descriptions: $this->descriptions,
        );
    }

    public function getSellersItemIdentification(): ?string
    {
        return $this->sellersItemIdentification;
    }

    public function withSellersItemIdentification(?string $sellersItemIdentification): self
    {
        return new self(
            buyersItemIdentification: $this->buyersItemIdentification,
            catalogueItemIdentification: $this->catalogueItemIdentification,
            sellersItemIdentification: $sellersItemIdentification,
            standardItemIdentification: $this->standardItemIdentification,
            descriptions: $this->descriptions,
        );
    }

    public function getStandardItemIdentification(): ?string
    {
        return $this->standardItemIdentification;
    }

    public function withStandardItemIdentification(?string $standardItemIdentification): self
    {
        return new self(
            buyersItemIdentification: $this->buyersItemIdentification,
            catalogueItemIdentification: $this->catalogueItemIdentification,
            sellersItemIdentification: $this->sellersItemIdentification,
            standardItemIdentification: $standardItemIdentification,
            descriptions: $this->descriptions,
        );
    }

    /**
     * @return list<DocumentDescription>
     */
    public function getDescriptions(): array
    {
        return $this->descriptions;
    }

    public function addDescription(DocumentDescription $description): self
    {
        $descriptions = $this->descriptions;
        $descriptions[] = $description;

        return new self(
            buyersItemIdentification: $this->buyersItemIdentification,
            catalogueItemIdentification: $this->catalogueItemIdentification,
            sellersItemIdentification: $this->sellersItemIdentification,
            standardItemIdentification: $this->standardItemIdentification,
            descriptions: $descriptions,
        );
    }

    /**
     * @param list<DocumentDescription> $descriptions
     */
    public function withDescriptions(array $descriptions): self
    {
        return new self(
            buyersItemIdentification: $this->buyersItemIdentification,
            catalogueItemIdentification: $this->catalogueItemIdentification,
            sellersItemIdentification: $this->sellersItemIdentification,
            standardItemIdentification: $this->standardItemIdentification,
            descriptions: $descriptions,
        );
    }
}
