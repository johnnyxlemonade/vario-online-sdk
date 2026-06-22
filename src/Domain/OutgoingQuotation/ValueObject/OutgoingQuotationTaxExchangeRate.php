<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\OutgoingQuotation\ValueObject;

use DateTimeImmutable;
use Lemonade\Vario\Domain\Common\Currency;

final class OutgoingQuotationTaxExchangeRate
{
    public function __construct(
        private readonly Currency $taxCurrency,
        private readonly float $referenceCurrencyRate,
        private readonly float $taxCurrencyRate,
        private readonly ?DateTimeImmutable $rateDate = null,
        private readonly ?string $exchangeMarketBic = null,
    ) {}

    public function getTaxCurrency(): Currency
    {
        return $this->taxCurrency;
    }

    public function getReferenceCurrencyRate(): float
    {
        return $this->referenceCurrencyRate;
    }

    public function getTaxCurrencyRate(): float
    {
        return $this->taxCurrencyRate;
    }

    public function getRateDate(): ?DateTimeImmutable
    {
        return $this->rateDate;
    }

    public function getExchangeMarketBic(): ?string
    {
        return $this->exchangeMarketBic;
    }
}
