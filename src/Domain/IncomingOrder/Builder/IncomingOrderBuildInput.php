<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Builder;

use DateTimeImmutable;
use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPaymentMeansCode;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineItemInput;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderPartiesInput;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxExchangeRate;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentIdentityInput;

final class IncomingOrderBuildInput
{
    /**
     * @param list<DocumentCalculatedLineInput<IncomingOrderLineItemInput>> $lines
     */
    public function __construct(
        private readonly DocumentIdentityInput $identity,
        private readonly DateTimeImmutable $issueDate,
        private readonly Currency $currency,
        private readonly IncomingOrderPartiesInput $parties,
        private readonly array $lines,
        private readonly bool $partialDeliveryIndicator = false,
        private readonly ?IncomingOrderPaymentMeansCode $paymentMeansCode = null,
        private readonly ?DocumentTaxExchangeRate $taxExchangeRate = null,
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

    public function getParties(): IncomingOrderPartiesInput
    {
        return $this->parties;
    }

    /**
     * @return list<DocumentCalculatedLineInput<IncomingOrderLineItemInput>>
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    public function isPartialDeliveryIndicator(): bool
    {
        return $this->partialDeliveryIndicator;
    }

    public function getPaymentMeansCode(): ?IncomingOrderPaymentMeansCode
    {
        return $this->paymentMeansCode;
    }

    public function getTaxExchangeRate(): ?DocumentTaxExchangeRate
    {
        return $this->taxExchangeRate;
    }
}
