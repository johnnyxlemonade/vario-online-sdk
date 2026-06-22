<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Write;

use DateTimeImmutable;
use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPaymentMeansCode;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentMonetaryTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxExchangeRate;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxTotal;

/**
 * Class IncomingOrderInput
 *
 * Mutable write model for IncomingOrder upsert payloads.
 *
 * This object represents a single order document sent to the Vario
 * IncomingOrder endpoint.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderInput
{
    /** @var list<IncomingOrderLineInput> */
    private array $documentLines = [];

    public function __construct(
        private string $uuid,
        private DateTimeImmutable $issueDate,
        private Currency $currency,
        private KnownPartyInput $buyerCustomerParty,
        private KnownPartyInput $sellerSupplierParty,
        private DocumentMonetaryTotal $monetaryTotal,
        private DocumentTaxExchangeRate $taxExchangeRate,
        private DocumentTaxTotal $taxTotal,
        private ?string $id = null,
        private ?KnownPartyInput $accountingCustomerParty = null,
        private ?KnownPartyInput $delivery = null,
        private ?string $note = null,
        private bool $partialDeliveryIndicator = false,
        private ?IncomingOrderPaymentMeansCode $paymentMeansCode = null,
    ) {}

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function withUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getIssueDate(): DateTimeImmutable
    {
        return $this->issueDate;
    }

    public function withIssueDate(DateTimeImmutable $issueDate): self
    {
        $this->issueDate = $issueDate;

        return $this;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function withCurrency(Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getBuyerCustomerParty(): KnownPartyInput
    {
        return $this->buyerCustomerParty;
    }

    public function withBuyerCustomerParty(KnownPartyInput $buyerCustomerParty): self
    {
        $this->buyerCustomerParty = $buyerCustomerParty;

        return $this;
    }

    public function getSellerSupplierParty(): KnownPartyInput
    {
        return $this->sellerSupplierParty;
    }

    public function withSellerSupplierParty(KnownPartyInput $sellerSupplierParty): self
    {
        $this->sellerSupplierParty = $sellerSupplierParty;

        return $this;
    }

    public function getMonetaryTotal(): DocumentMonetaryTotal
    {
        return $this->monetaryTotal;
    }

    public function withMonetaryTotal(DocumentMonetaryTotal $monetaryTotal): self
    {
        $this->monetaryTotal = $monetaryTotal;

        return $this;
    }

    public function getTaxExchangeRate(): DocumentTaxExchangeRate
    {
        return $this->taxExchangeRate;
    }

    public function withTaxExchangeRate(DocumentTaxExchangeRate $taxExchangeRate): self
    {
        $this->taxExchangeRate = $taxExchangeRate;

        return $this;
    }

    public function getTaxTotal(): DocumentTaxTotal
    {
        return $this->taxTotal;
    }

    public function withTaxTotal(DocumentTaxTotal $taxTotal): self
    {
        $this->taxTotal = $taxTotal;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function withId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getAccountingCustomerParty(): ?KnownPartyInput
    {
        return $this->accountingCustomerParty;
    }

    public function withAccountingCustomerParty(?KnownPartyInput $accountingCustomerParty): self
    {
        $this->accountingCustomerParty = $accountingCustomerParty;

        return $this;
    }

    public function getDelivery(): ?KnownPartyInput
    {
        return $this->delivery;
    }

    public function withDelivery(?KnownPartyInput $delivery): self
    {
        $this->delivery = $delivery;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function withNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function isPartialDeliveryIndicator(): bool
    {
        return $this->partialDeliveryIndicator;
    }

    public function withPartialDeliveryIndicator(bool $partialDeliveryIndicator): self
    {
        $this->partialDeliveryIndicator = $partialDeliveryIndicator;

        return $this;
    }

    public function getPaymentMeansCode(): ?IncomingOrderPaymentMeansCode
    {
        return $this->paymentMeansCode;
    }

    public function withPaymentMeansCode(?IncomingOrderPaymentMeansCode $paymentMeansCode): self
    {
        $this->paymentMeansCode = $paymentMeansCode;

        return $this;
    }

    /**
     * @return list<IncomingOrderLineInput>
     */
    public function getDocumentLines(): array
    {
        return $this->documentLines;
    }

    public function addDocumentLine(IncomingOrderLineInput $documentLine): self
    {
        $this->documentLines[] = $documentLine;

        return $this;
    }

    /**
     * @param list<IncomingOrderLineInput> $documentLines
     */
    public function withDocumentLines(array $documentLines): self
    {
        $this->documentLines = $documentLines;

        return $this;
    }
}
