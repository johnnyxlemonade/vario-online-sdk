<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Read;

use DateTimeImmutable;
use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPaymentMeansCode;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentMonetaryTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxExchangeRate;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxTotal;

/**
 * Class IncomingOrder
 *
 * Immutable domain read model representing an order returned
 * by the Vario IncomingOrder API.
 *
 * Instances of this class are created by the IncomingOrderMapper
 * when converting raw Vario API payloads into strongly-typed
 * domain objects.
 *
 * The model intentionally exposes stable core properties such as:
 *
 * - UUID / ID
 * - issue date
 * - currency
 * - business parties
 * - document lines
 * - totals and tax summary
 * - delivery detail
 * - top-level textual attributes
 *
 * Any additional fields returned by the API are preserved in the
 * `$extra` payload to maintain forward compatibility with newer
 * API versions or custom API extensions.
 *
 * IncomingOrder acts as the primary read model returned by:
 *
 *     IncomingOrderApi::query()
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrder
{
    /**
     * @param list<IncomingOrderLine> $documentLines
     * @param list<IncomingOrderTextualAttribute> $textualAttributes
     * @param array<string,mixed> $extra Additional unmapped API fields.
     */
    public function __construct(
        // =========================
        // ENTITY IDENTITY (required)
        // =========================
        private readonly string $uuid,

        // =========================
        // CORE ATTRIBUTES (optional)
        // =========================
        private readonly ?string $id = null,
        private readonly ?DateTimeImmutable $issueDate = null,
        private readonly ?Currency $currency = null,
        private readonly ?string $note = null,
        private readonly ?bool $partialDeliveryIndicator = null,
        private readonly ?IncomingOrderPaymentMeansCode $paymentMeansCode = null,

        // =========================
        // BUSINESS PARTIES
        // =========================
        private readonly ?IncomingOrderParty $buyerCustomerParty = null,
        private readonly ?IncomingOrderParty $accountingCustomerParty = null,
        private readonly ?IncomingOrderParty $delivery = null,
        private readonly ?IncomingOrderParty $sellerSupplierParty = null,

        // =========================
        // DOCUMENT CONTENT
        // =========================
        private readonly array $documentLines = [],
        private readonly ?DocumentMonetaryTotal $monetaryTotal = null,
        private readonly ?DocumentTaxExchangeRate $taxExchangeRate = null,
        private readonly ?DocumentTaxTotal $taxTotal = null,
        private readonly ?IncomingOrderDeliveryDetail $deliveryDetail = null,
        private readonly array $textualAttributes = [],

        // =========================
        // FORWARD COMPATIBILITY
        // =========================
        private readonly array $extra = [],
    ) {}

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getIssueDate(): ?DateTimeImmutable
    {
        return $this->issueDate;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getPartialDeliveryIndicator(): ?bool
    {
        return $this->partialDeliveryIndicator;
    }

    public function getPaymentMeansCode(): ?IncomingOrderPaymentMeansCode
    {
        return $this->paymentMeansCode;
    }

    public function getBuyerCustomerParty(): ?IncomingOrderParty
    {
        return $this->buyerCustomerParty;
    }

    public function getAccountingCustomerParty(): ?IncomingOrderParty
    {
        return $this->accountingCustomerParty;
    }

    public function getDelivery(): ?IncomingOrderParty
    {
        return $this->delivery;
    }

    public function getSellerSupplierParty(): ?IncomingOrderParty
    {
        return $this->sellerSupplierParty;
    }

    /**
     * @return list<IncomingOrderLine>
     */
    public function getDocumentLines(): array
    {
        return $this->documentLines;
    }

    public function hasDocumentLines(): bool
    {
        return $this->documentLines !== [];
    }

    public function hasMonetaryTotal(): bool
    {
        return $this->monetaryTotal !== null;
    }

    public function getMonetaryTotal(): ?DocumentMonetaryTotal
    {
        return $this->monetaryTotal;
    }

    public function getTaxExchangeRate(): ?DocumentTaxExchangeRate
    {
        return $this->taxExchangeRate;
    }

    public function hasTaxTotal(): bool
    {
        return $this->taxTotal !== null;
    }

    public function getTaxTotal(): ?DocumentTaxTotal
    {
        return $this->taxTotal;
    }

    public function hasDeliveryDetail(): bool
    {
        return $this->deliveryDetail !== null;
    }

    public function getDeliveryDetail(): ?IncomingOrderDeliveryDetail
    {
        return $this->deliveryDetail;
    }

    /**
     * @return list<IncomingOrderTextualAttribute>
     */
    public function getTextualAttributes(): array
    {
        return $this->textualAttributes;
    }

    public function hasTextualAttributes(): bool
    {
        return $this->textualAttributes !== [];
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
