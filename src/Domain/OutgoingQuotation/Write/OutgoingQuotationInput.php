<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\OutgoingQuotation\Write;

use DateTimeImmutable;
use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Enum\OutgoingQuotationPaymentMeansCode;
use Lemonade\Vario\Domain\OutgoingQuotation\ValueObject\OutgoingQuotationMonetaryTotal;
use Lemonade\Vario\Domain\OutgoingQuotation\ValueObject\OutgoingQuotationTaxExchangeRate;
use Lemonade\Vario\Domain\OutgoingQuotation\ValueObject\OutgoingQuotationTaxTotal;

final class OutgoingQuotationInput
{
    /**
     * @param list<OutgoingQuotationLineInput> $documentLines
     */
    public function __construct(
        private readonly string $uuid,
        private readonly DateTimeImmutable $issueDate,
        private readonly Currency $currency,
        private readonly KnownPartyInput $buyerCustomerParty,
        private readonly KnownPartyInput $sellerSupplierParty,
        private readonly OutgoingQuotationMonetaryTotal $monetaryTotal,
        private readonly OutgoingQuotationTaxExchangeRate $taxExchangeRate,
        private readonly OutgoingQuotationTaxTotal $taxTotal,
        private readonly array $documentLines,
        private readonly ?string $id = null,
        private readonly ?string $note = null,
        private readonly ?OutgoingQuotationPaymentMeansCode $paymentMeansCode = null,
    ) {}

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function withUuid(string $uuid): self
    {
        return new self(
            uuid: $uuid,
            issueDate: $this->issueDate,
            currency: $this->currency,
            buyerCustomerParty: $this->buyerCustomerParty,
            sellerSupplierParty: $this->sellerSupplierParty,
            monetaryTotal: $this->monetaryTotal,
            taxExchangeRate: $this->taxExchangeRate,
            taxTotal: $this->taxTotal,
            documentLines: $this->documentLines,
            id: $this->id,
            note: $this->note,
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
            uuid: $this->uuid,
            issueDate: $issueDate,
            currency: $this->currency,
            buyerCustomerParty: $this->buyerCustomerParty,
            sellerSupplierParty: $this->sellerSupplierParty,
            monetaryTotal: $this->monetaryTotal,
            taxExchangeRate: $this->taxExchangeRate,
            taxTotal: $this->taxTotal,
            documentLines: $this->documentLines,
            id: $this->id,
            note: $this->note,
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
            uuid: $this->uuid,
            issueDate: $this->issueDate,
            currency: $currency,
            buyerCustomerParty: $this->buyerCustomerParty,
            sellerSupplierParty: $this->sellerSupplierParty,
            monetaryTotal: $this->monetaryTotal,
            taxExchangeRate: $this->taxExchangeRate,
            taxTotal: $this->taxTotal,
            documentLines: $this->documentLines,
            id: $this->id,
            note: $this->note,
            paymentMeansCode: $this->paymentMeansCode,
        );
    }

    public function getBuyerCustomerParty(): KnownPartyInput
    {
        return $this->buyerCustomerParty;
    }

    public function withBuyerCustomerParty(KnownPartyInput $buyerCustomerParty): self
    {
        return new self(
            uuid: $this->uuid,
            issueDate: $this->issueDate,
            currency: $this->currency,
            buyerCustomerParty: $buyerCustomerParty,
            sellerSupplierParty: $this->sellerSupplierParty,
            monetaryTotal: $this->monetaryTotal,
            taxExchangeRate: $this->taxExchangeRate,
            taxTotal: $this->taxTotal,
            documentLines: $this->documentLines,
            id: $this->id,
            note: $this->note,
            paymentMeansCode: $this->paymentMeansCode,
        );
    }

    public function getSellerSupplierParty(): KnownPartyInput
    {
        return $this->sellerSupplierParty;
    }

    public function withSellerSupplierParty(KnownPartyInput $sellerSupplierParty): self
    {
        return new self(
            uuid: $this->uuid,
            issueDate: $this->issueDate,
            currency: $this->currency,
            buyerCustomerParty: $this->buyerCustomerParty,
            sellerSupplierParty: $sellerSupplierParty,
            monetaryTotal: $this->monetaryTotal,
            taxExchangeRate: $this->taxExchangeRate,
            taxTotal: $this->taxTotal,
            documentLines: $this->documentLines,
            id: $this->id,
            note: $this->note,
            paymentMeansCode: $this->paymentMeansCode,
        );
    }

    public function getMonetaryTotal(): OutgoingQuotationMonetaryTotal
    {
        return $this->monetaryTotal;
    }

    public function withMonetaryTotal(OutgoingQuotationMonetaryTotal $monetaryTotal): self
    {
        return new self(
            uuid: $this->uuid,
            issueDate: $this->issueDate,
            currency: $this->currency,
            buyerCustomerParty: $this->buyerCustomerParty,
            sellerSupplierParty: $this->sellerSupplierParty,
            monetaryTotal: $monetaryTotal,
            taxExchangeRate: $this->taxExchangeRate,
            taxTotal: $this->taxTotal,
            documentLines: $this->documentLines,
            id: $this->id,
            note: $this->note,
            paymentMeansCode: $this->paymentMeansCode,
        );
    }

    public function getTaxExchangeRate(): OutgoingQuotationTaxExchangeRate
    {
        return $this->taxExchangeRate;
    }

    public function withTaxExchangeRate(OutgoingQuotationTaxExchangeRate $taxExchangeRate): self
    {
        return new self(
            uuid: $this->uuid,
            issueDate: $this->issueDate,
            currency: $this->currency,
            buyerCustomerParty: $this->buyerCustomerParty,
            sellerSupplierParty: $this->sellerSupplierParty,
            monetaryTotal: $this->monetaryTotal,
            taxExchangeRate: $taxExchangeRate,
            taxTotal: $this->taxTotal,
            documentLines: $this->documentLines,
            id: $this->id,
            note: $this->note,
            paymentMeansCode: $this->paymentMeansCode,
        );
    }

    public function getTaxTotal(): OutgoingQuotationTaxTotal
    {
        return $this->taxTotal;
    }

    public function withTaxTotal(OutgoingQuotationTaxTotal $taxTotal): self
    {
        return new self(
            uuid: $this->uuid,
            issueDate: $this->issueDate,
            currency: $this->currency,
            buyerCustomerParty: $this->buyerCustomerParty,
            sellerSupplierParty: $this->sellerSupplierParty,
            monetaryTotal: $this->monetaryTotal,
            taxExchangeRate: $this->taxExchangeRate,
            taxTotal: $taxTotal,
            documentLines: $this->documentLines,
            id: $this->id,
            note: $this->note,
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
            uuid: $this->uuid,
            issueDate: $this->issueDate,
            currency: $this->currency,
            buyerCustomerParty: $this->buyerCustomerParty,
            sellerSupplierParty: $this->sellerSupplierParty,
            monetaryTotal: $this->monetaryTotal,
            taxExchangeRate: $this->taxExchangeRate,
            taxTotal: $this->taxTotal,
            documentLines: $documentLines,
            id: $this->id,
            note: $this->note,
            paymentMeansCode: $this->paymentMeansCode,
        );
    }

    public function addDocumentLine(OutgoingQuotationLineInput $documentLine): self
    {
        $documentLines = $this->documentLines;
        $documentLines[] = $documentLine;

        return new self(
            uuid: $this->uuid,
            issueDate: $this->issueDate,
            currency: $this->currency,
            buyerCustomerParty: $this->buyerCustomerParty,
            sellerSupplierParty: $this->sellerSupplierParty,
            monetaryTotal: $this->monetaryTotal,
            taxExchangeRate: $this->taxExchangeRate,
            taxTotal: $this->taxTotal,
            documentLines: $documentLines,
            id: $this->id,
            note: $this->note,
            paymentMeansCode: $this->paymentMeansCode,
        );
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function withId(?string $id): self
    {
        return new self(
            uuid: $this->uuid,
            issueDate: $this->issueDate,
            currency: $this->currency,
            buyerCustomerParty: $this->buyerCustomerParty,
            sellerSupplierParty: $this->sellerSupplierParty,
            monetaryTotal: $this->monetaryTotal,
            taxExchangeRate: $this->taxExchangeRate,
            taxTotal: $this->taxTotal,
            documentLines: $this->documentLines,
            id: $id,
            note: $this->note,
            paymentMeansCode: $this->paymentMeansCode,
        );
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function withNote(?string $note): self
    {
        return new self(
            uuid: $this->uuid,
            issueDate: $this->issueDate,
            currency: $this->currency,
            buyerCustomerParty: $this->buyerCustomerParty,
            sellerSupplierParty: $this->sellerSupplierParty,
            monetaryTotal: $this->monetaryTotal,
            taxExchangeRate: $this->taxExchangeRate,
            taxTotal: $this->taxTotal,
            documentLines: $this->documentLines,
            id: $this->id,
            note: $note,
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
            uuid: $this->uuid,
            issueDate: $this->issueDate,
            currency: $this->currency,
            buyerCustomerParty: $this->buyerCustomerParty,
            sellerSupplierParty: $this->sellerSupplierParty,
            monetaryTotal: $this->monetaryTotal,
            taxExchangeRate: $this->taxExchangeRate,
            taxTotal: $this->taxTotal,
            documentLines: $this->documentLines,
            id: $this->id,
            note: $this->note,
            paymentMeansCode: $paymentMeansCode,
        );
    }
}
