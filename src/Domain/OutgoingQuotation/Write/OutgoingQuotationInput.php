<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\OutgoingQuotation\Write;

use DateTimeImmutable;
use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Enum\OutgoingQuotationPaymentMeansCode;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentMonetaryTotal;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxExchangeRate;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxTotal;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentIdentityInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentTotalsInput;

final class OutgoingQuotationInput
{
    /**
     * @param list<OutgoingQuotationLineInput> $documentLines
     */
    public function __construct(
        private readonly DocumentIdentityInput $identity,
        private readonly DateTimeImmutable $issueDate,
        private readonly Currency $currency,
        private readonly OutgoingQuotationPartiesInput $parties,
        private readonly DocumentTotalsInput $totals,
        private readonly array $documentLines,
        private readonly ?OutgoingQuotationPaymentMeansCode $paymentMeansCode = null,
    ) {}

    public function getUuid(): string
    {
        return $this->identity->getUuid();
    }

    public function withUuid(string $uuid): self
    {
        return new self(
            identity: new DocumentIdentityInput(
                uuid: $uuid,
                id: $this->identity->getId(),
                note: $this->identity->getNote(),
            ),
            issueDate: $this->issueDate,
            currency: $this->currency,
            parties: $this->parties,
            totals: $this->totals,
            documentLines: $this->documentLines,
            paymentMeansCode: $this->paymentMeansCode,
        );
    }

    public function getIssueDate(): DateTimeImmutable
    {
        return $this->issueDate;
    }

    public function withIssueDate(DateTimeImmutable $issueDate): self
    {
        return new self(
            identity: $this->identity,
            issueDate: $issueDate,
            currency: $this->currency,
            parties: $this->parties,
            totals: $this->totals,
            documentLines: $this->documentLines,
            paymentMeansCode: $this->paymentMeansCode,
        );
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function withCurrency(Currency $currency): self
    {
        return new self(
            identity: $this->identity,
            issueDate: $this->issueDate,
            currency: $currency,
            parties: $this->parties,
            totals: $this->totals,
            documentLines: $this->documentLines,
            paymentMeansCode: $this->paymentMeansCode,
        );
    }

    public function getBuyerCustomerParty(): KnownPartyInput
    {
        return $this->parties->getBuyerCustomerParty();
    }

    public function withBuyerCustomerParty(KnownPartyInput $buyerCustomerParty): self
    {
        return new self(
            identity: $this->identity,
            issueDate: $this->issueDate,
            currency: $this->currency,
            parties: new OutgoingQuotationPartiesInput(
                buyerCustomerParty: $buyerCustomerParty,
                sellerSupplierParty: $this->parties->getSellerSupplierParty(),
            ),
            totals: $this->totals,
            documentLines: $this->documentLines,
            paymentMeansCode: $this->paymentMeansCode,
        );
    }

    public function getSellerSupplierParty(): KnownPartyInput
    {
        return $this->parties->getSellerSupplierParty();
    }

    public function withSellerSupplierParty(KnownPartyInput $sellerSupplierParty): self
    {
        return new self(
            identity: $this->identity,
            issueDate: $this->issueDate,
            currency: $this->currency,
            parties: new OutgoingQuotationPartiesInput(
                buyerCustomerParty: $this->parties->getBuyerCustomerParty(),
                sellerSupplierParty: $sellerSupplierParty,
            ),
            totals: $this->totals,
            documentLines: $this->documentLines,
            paymentMeansCode: $this->paymentMeansCode,
        );
    }

    public function getMonetaryTotal(): DocumentMonetaryTotal
    {
        return $this->totals->getMonetaryTotal();
    }

    public function withMonetaryTotal(DocumentMonetaryTotal $monetaryTotal): self
    {
        return new self(
            identity: $this->identity,
            issueDate: $this->issueDate,
            currency: $this->currency,
            parties: $this->parties,
            totals: new DocumentTotalsInput(
                monetaryTotal: $monetaryTotal,
                taxExchangeRate: $this->totals->getTaxExchangeRate(),
                taxTotal: $this->totals->getTaxTotal(),
            ),
            documentLines: $this->documentLines,
            paymentMeansCode: $this->paymentMeansCode,
        );
    }

    public function getTaxExchangeRate(): DocumentTaxExchangeRate
    {
        return $this->totals->getTaxExchangeRate();
    }

    public function withTaxExchangeRate(DocumentTaxExchangeRate $taxExchangeRate): self
    {
        return new self(
            identity: $this->identity,
            issueDate: $this->issueDate,
            currency: $this->currency,
            parties: $this->parties,
            totals: new DocumentTotalsInput(
                monetaryTotal: $this->totals->getMonetaryTotal(),
                taxExchangeRate: $taxExchangeRate,
                taxTotal: $this->totals->getTaxTotal(),
            ),
            documentLines: $this->documentLines,
            paymentMeansCode: $this->paymentMeansCode,
        );
    }

    public function getTaxTotal(): DocumentTaxTotal
    {
        return $this->totals->getTaxTotal();
    }

    public function withTaxTotal(DocumentTaxTotal $taxTotal): self
    {
        return new self(
            identity: $this->identity,
            issueDate: $this->issueDate,
            currency: $this->currency,
            parties: $this->parties,
            totals: new DocumentTotalsInput(
                monetaryTotal: $this->totals->getMonetaryTotal(),
                taxExchangeRate: $this->totals->getTaxExchangeRate(),
                taxTotal: $taxTotal,
            ),
            documentLines: $this->documentLines,
            paymentMeansCode: $this->paymentMeansCode,
        );
    }

    /**
     * @return list<OutgoingQuotationLineInput>
     */
    public function getDocumentLines(): array
    {
        return $this->documentLines;
    }

    /**
     * @param list<OutgoingQuotationLineInput> $documentLines
     */
    public function withDocumentLines(array $documentLines): self
    {
        return new self(
            identity: $this->identity,
            issueDate: $this->issueDate,
            currency: $this->currency,
            parties: $this->parties,
            totals: $this->totals,
            documentLines: $documentLines,
            paymentMeansCode: $this->paymentMeansCode,
        );
    }

    public function addDocumentLine(OutgoingQuotationLineInput $documentLine): self
    {
        $documentLines = $this->documentLines;
        $documentLines[] = $documentLine;

        return new self(
            identity: $this->identity,
            issueDate: $this->issueDate,
            currency: $this->currency,
            parties: $this->parties,
            totals: $this->totals,
            documentLines: $documentLines,
            paymentMeansCode: $this->paymentMeansCode,
        );
    }

    public function getId(): ?string
    {
        return $this->identity->getId();
    }

    public function withId(?string $id): self
    {
        return new self(
            identity: new DocumentIdentityInput(
                uuid: $this->identity->getUuid(),
                id: $id,
                note: $this->identity->getNote(),
            ),
            issueDate: $this->issueDate,
            currency: $this->currency,
            parties: $this->parties,
            totals: $this->totals,
            documentLines: $this->documentLines,
            paymentMeansCode: $this->paymentMeansCode,
        );
    }

    public function getNote(): ?string
    {
        return $this->identity->getNote();
    }

    public function withNote(?string $note): self
    {
        return new self(
            identity: new DocumentIdentityInput(
                uuid: $this->identity->getUuid(),
                id: $this->identity->getId(),
                note: $note,
            ),
            issueDate: $this->issueDate,
            currency: $this->currency,
            parties: $this->parties,
            totals: $this->totals,
            documentLines: $this->documentLines,
            paymentMeansCode: $this->paymentMeansCode,
        );
    }

    public function getPaymentMeansCode(): ?OutgoingQuotationPaymentMeansCode
    {
        return $this->paymentMeansCode;
    }

    public function withPaymentMeansCode(?OutgoingQuotationPaymentMeansCode $paymentMeansCode): self
    {
        return new self(
            identity: $this->identity,
            issueDate: $this->issueDate,
            currency: $this->currency,
            parties: $this->parties,
            totals: $this->totals,
            documentLines: $this->documentLines,
            paymentMeansCode: $paymentMeansCode,
        );
    }
}
