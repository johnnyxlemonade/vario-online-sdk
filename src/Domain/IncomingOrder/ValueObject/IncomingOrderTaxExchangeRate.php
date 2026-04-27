<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\ValueObject;

use DateTimeImmutable;
use Lemonade\Vario\Domain\Common\Currency;

/**
 * Class IncomingOrderTaxExchangeRate
 *
 * Tax exchange rate for IncomingOrder payloads.
 *
 * Tax totals are expressed in tax currency and this object defines
 * the conversion between document currency and tax currency.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderTaxExchangeRate
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
