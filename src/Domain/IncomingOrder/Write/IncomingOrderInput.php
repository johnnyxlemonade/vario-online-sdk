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
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentIdentityInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentTotalsInput;

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
        private DocumentIdentityInput $identity,
        private DateTimeImmutable $issueDate,
        private Currency $currency,
        private IncomingOrderPartiesInput $parties,
        private DocumentTotalsInput $totals,
        private bool $partialDeliveryIndicator = false,
        private ?IncomingOrderPaymentMeansCode $paymentMeansCode = null,
    ) {}

    public function getUuid(): string
    {
        return $this->identity->getUuid();
    }

    public function withUuid(string $uuid): self
    {
        $this->identity = new DocumentIdentityInput(
            uuid: $uuid,
            id: $this->identity->getId(),
            note: $this->identity->getNote(),
        );

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
        return $this->parties->getBuyerCustomerParty();
    }

    public function withBuyerCustomerParty(KnownPartyInput $buyerCustomerParty): self
    {
        $this->parties = new IncomingOrderPartiesInput(
            buyerCustomerParty: $buyerCustomerParty,
            sellerSupplierParty: $this->parties->getSellerSupplierParty(),
            accountingCustomerParty: $this->parties->getAccountingCustomerParty(),
            delivery: $this->parties->getDelivery(),
        );

        return $this;
    }

    public function getSellerSupplierParty(): KnownPartyInput
    {
        return $this->parties->getSellerSupplierParty();
    }

    public function withSellerSupplierParty(KnownPartyInput $sellerSupplierParty): self
    {
        $this->parties = new IncomingOrderPartiesInput(
            buyerCustomerParty: $this->parties->getBuyerCustomerParty(),
            sellerSupplierParty: $sellerSupplierParty,
            accountingCustomerParty: $this->parties->getAccountingCustomerParty(),
            delivery: $this->parties->getDelivery(),
        );

        return $this;
    }

    public function getMonetaryTotal(): DocumentMonetaryTotal
    {
        return $this->totals->getMonetaryTotal();
    }

    public function withMonetaryTotal(DocumentMonetaryTotal $monetaryTotal): self
    {
        $this->totals = new DocumentTotalsInput(
            monetaryTotal: $monetaryTotal,
            taxExchangeRate: $this->totals->getTaxExchangeRate(),
            taxTotal: $this->totals->getTaxTotal(),
        );

        return $this;
    }

    public function getTaxExchangeRate(): DocumentTaxExchangeRate
    {
        return $this->totals->getTaxExchangeRate();
    }

    public function withTaxExchangeRate(DocumentTaxExchangeRate $taxExchangeRate): self
    {
        $this->totals = new DocumentTotalsInput(
            monetaryTotal: $this->totals->getMonetaryTotal(),
            taxExchangeRate: $taxExchangeRate,
            taxTotal: $this->totals->getTaxTotal(),
        );

        return $this;
    }

    public function getTaxTotal(): DocumentTaxTotal
    {
        return $this->totals->getTaxTotal();
    }

    public function withTaxTotal(DocumentTaxTotal $taxTotal): self
    {
        $this->totals = new DocumentTotalsInput(
            monetaryTotal: $this->totals->getMonetaryTotal(),
            taxExchangeRate: $this->totals->getTaxExchangeRate(),
            taxTotal: $taxTotal,
        );

        return $this;
    }

    public function getId(): ?string
    {
        return $this->identity->getId();
    }

    public function withId(?string $id): self
    {
        $this->identity = new DocumentIdentityInput(
            uuid: $this->identity->getUuid(),
            id: $id,
            note: $this->identity->getNote(),
        );

        return $this;
    }

    public function getAccountingCustomerParty(): ?KnownPartyInput
    {
        return $this->parties->getAccountingCustomerParty();
    }

    public function withAccountingCustomerParty(?KnownPartyInput $accountingCustomerParty): self
    {
        $this->parties = new IncomingOrderPartiesInput(
            buyerCustomerParty: $this->parties->getBuyerCustomerParty(),
            sellerSupplierParty: $this->parties->getSellerSupplierParty(),
            accountingCustomerParty: $accountingCustomerParty,
            delivery: $this->parties->getDelivery(),
        );

        return $this;
    }

    public function getDelivery(): ?KnownPartyInput
    {
        return $this->parties->getDelivery();
    }

    public function withDelivery(?KnownPartyInput $delivery): self
    {
        $this->parties = new IncomingOrderPartiesInput(
            buyerCustomerParty: $this->parties->getBuyerCustomerParty(),
            sellerSupplierParty: $this->parties->getSellerSupplierParty(),
            accountingCustomerParty: $this->parties->getAccountingCustomerParty(),
            delivery: $delivery,
        );

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->identity->getNote();
    }

    public function withNote(?string $note): self
    {
        $this->identity = new DocumentIdentityInput(
            uuid: $this->identity->getUuid(),
            id: $this->identity->getId(),
            note: $note,
        );

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
