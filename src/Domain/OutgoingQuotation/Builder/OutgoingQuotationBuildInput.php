<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\OutgoingQuotation\Builder;

use DateTimeImmutable;
use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\OutgoingQuotation\Enum\OutgoingQuotationPaymentMeansCode;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationLineItemInput;
use Lemonade\Vario\Domain\OutgoingQuotation\Write\OutgoingQuotationPartiesInput;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxExchangeRate;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentIdentityInput;

final class OutgoingQuotationBuildInput
{
    /**
     * @param list<DocumentCalculatedLineInput<OutgoingQuotationLineItemInput>> $lines
     */
    public function __construct(
        private readonly DocumentIdentityInput $identity,
        private readonly DateTimeImmutable $issueDate,
        private readonly Currency $currency,
        private readonly OutgoingQuotationPartiesInput $parties,
        private readonly array $lines,
        private readonly ?OutgoingQuotationPaymentMeansCode $paymentMeansCode = null,
        private readonly ?DocumentTaxExchangeRate $taxExchangeRate = null,
        private readonly float $payableRoundingAmount = 0.0,
    ) {}

    public function getIdentity(): DocumentIdentityInput
    {
        return $this->identity;
    }

    public function getIssueDate(): DateTimeImmutable
    {
        return $this->issueDate;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getParties(): OutgoingQuotationPartiesInput
    {
        return $this->parties;
    }

    /**
     * @return list<DocumentCalculatedLineInput<OutgoingQuotationLineItemInput>>
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    public function getPaymentMeansCode(): ?OutgoingQuotationPaymentMeansCode
    {
        return $this->paymentMeansCode;
    }

    public function getTaxExchangeRate(): ?DocumentTaxExchangeRate
    {
        return $this->taxExchangeRate;
    }

    public function getPayableRoundingAmount(): float
    {
        return $this->payableRoundingAmount;
    }
}
