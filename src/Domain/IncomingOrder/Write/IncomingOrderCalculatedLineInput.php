<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Write;

use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPriceMode;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTaxCalculationMethod;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTaxScheme;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderUnitOfMeasureScheme;

/**
 * Class IncomingOrderCalculatedLineInput
 *
 * High-level input for a calculated IncomingOrder line.
 *
 * This DTO is intended for developer-friendly order construction,
 * where only quantity, unit price and VAT mode are provided,
 * and the low-level line totals are calculated automatically.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
final class IncomingOrderCalculatedLineInput
{
    public function __construct(
        private readonly string $uuid,
        private readonly IncomingOrderLineItemInput $lineItem,
        private readonly float $quantity,
        private readonly string $unitCode,
        private readonly float $unitPrice,
        private readonly float $vatRate,
        private readonly IncomingOrderPriceMode $priceMode = IncomingOrderPriceMode::WithoutVat,
        private readonly IncomingOrderUnitOfMeasureScheme $unitScheme = IncomingOrderUnitOfMeasureScheme::Unknown,
        private readonly ?string $id = null,
        private readonly ?string $note = null,
        private readonly IncomingOrderTaxCalculationMethod $taxCalculationMethod = IncomingOrderTaxCalculationMethod::Add,
        private readonly IncomingOrderTaxScheme $taxScheme = IncomingOrderTaxScheme::Vat,
        private readonly ?string $taxSchemeExtensionCode = null,
    ) {}

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getLineItem(): IncomingOrderLineItemInput
    {
        return $this->lineItem;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function getUnitCode(): string
    {
        return $this->unitCode;
    }

    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    public function getVatRate(): float
    {
        return $this->vatRate;
    }

    public function getPriceMode(): IncomingOrderPriceMode
    {
        return $this->priceMode;
    }

    public function getUnitScheme(): IncomingOrderUnitOfMeasureScheme
    {
        return $this->unitScheme;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getTaxCalculationMethod(): IncomingOrderTaxCalculationMethod
    {
        return $this->taxCalculationMethod;
    }

    public function getTaxScheme(): IncomingOrderTaxScheme
    {
        return $this->taxScheme;
    }

    public function getTaxSchemeExtensionCode(): ?string
    {
        return $this->taxSchemeExtensionCode;
    }
}
