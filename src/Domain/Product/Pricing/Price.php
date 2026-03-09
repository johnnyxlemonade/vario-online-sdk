<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Product\Pricing;

use Lemonade\Vario\Domain\Common\Currency;
use Lemonade\Vario\Domain\Common\VatRate;
use Lemonade\Vario\Domain\Product\ValueObject\ProductSection;

/**
 * Class Price
 *
 * Represents a single product price value.
 *
 * Encapsulates the numeric price together with VAT information
 * and optional currency metadata.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\Product
 * @category    Domain
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak
 * @license     MIT
 * @since       1.0
 */
final class Price implements ProductSection
{
    public function __construct(
        private readonly float $value,
        private readonly bool $includesVat,
        private readonly ?VatRate $vatRate = null,
        private readonly ?Currency $currency = null
    ) {}

    public function getValue(): float
    {
        return $this->value;
    }

    public function isVatIncluded(): bool
    {
        return $this->includesVat;
    }

    public function isGross(): bool
    {
        return $this->includesVat;
    }

    public function getVatRate(): ?VatRate
    {
        return $this->vatRate;
    }

    public function getVatPercentage(): ?float
    {
        return $this->vatRate?->percentage();
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    /**
     * @return array{
     *     value: float,
     *     includesVat: bool,
     *     vatRate: ?float,
     *     currency: ?string
     * }
     */
    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'includesVat' => $this->includesVat,
            'vatRate' => $this->vatRate?->percentage(),
            'currency' => $this->currency?->value,
        ];
    }
}
