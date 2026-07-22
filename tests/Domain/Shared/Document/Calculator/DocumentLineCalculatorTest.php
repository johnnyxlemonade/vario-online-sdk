<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Shared\Document\Calculator;

use InvalidArgumentException;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderLineItemInput;
use Lemonade\Vario\Domain\Shared\Document\Calculator\DocumentLineCalculator;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentPriceMode;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxCalculationMethod;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxScheme;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineIdentityInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLinePriceInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineQuantityInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineTaxInput;
use PHPUnit\Framework\TestCase;

final class DocumentLineCalculatorTest extends TestCase
{
    public function test_calculate_builds_values_from_tax_exclusive_price(): void
    {
        $calculator = new DocumentLineCalculator();

        $result = $calculator->calculate(new DocumentCalculatedLineInput(
            identity: new DocumentCalculatedLineIdentityInput(
                uuid: 'line-1',
                id: 'ROW-1',
                note: 'row note',
            ),
            lineItem: new IncomingOrderLineItemInput(),
            quantity: new DocumentCalculatedLineQuantityInput(
                value: 2.0,
                unitCode: 'Ks',
                scheme: DocumentUnitOfMeasureScheme::Unknown,
            ),
            price: new DocumentCalculatedLinePriceInput(
                unitPrice: 100.0,
                vatRate: 21.0,
                priceMode: DocumentPriceMode::WithoutVat,
            ),
            tax: new DocumentCalculatedLineTaxInput(
                calculationMethod: DocumentTaxCalculationMethod::Add,
                scheme: DocumentTaxScheme::Vat,
                schemeExtensionCode: 'LOCAL-RC',
            ),
        ));

        self::assertSame(200.0, $result['lineExtensionAmount']);
        self::assertSame(242.0, $result['lineExtensionAmountTaxInclusive']);
        self::assertSame(2.0, $result['lineQuantity']->getValue());
        self::assertSame('Ks', $result['lineQuantity']->getUnitCode());
        self::assertSame(DocumentUnitOfMeasureScheme::Unknown, $result['lineQuantity']->getScheme());
        self::assertSame(DocumentTaxCalculationMethod::Add, $result['taxSubTotal']->getCalculationMethod());
        self::assertSame(DocumentTaxScheme::Vat, $result['taxSubTotal']->getScheme());
        self::assertSame(200.0, $result['taxSubTotal']->getTaxableAmount());
        self::assertSame(42.0, $result['taxSubTotal']->getTaxAmount());
        self::assertSame(21.0, $result['taxSubTotal']->getTaxPercentage());
        self::assertSame('LOCAL-RC', $result['taxSubTotal']->getTaxSchemeExtensionCode());
    }

    public function test_calculate_builds_values_from_tax_inclusive_price(): void
    {
        $calculator = new DocumentLineCalculator();

        $result = $calculator->calculate(new DocumentCalculatedLineInput(
            identity: new DocumentCalculatedLineIdentityInput(
                uuid: 'line-2',
            ),
            lineItem: new IncomingOrderLineItemInput(),
            quantity: new DocumentCalculatedLineQuantityInput(
                value: 2.0,
                unitCode: 'Ks',
            ),
            price: new DocumentCalculatedLinePriceInput(
                unitPrice: 121.0,
                vatRate: 21.0,
                priceMode: DocumentPriceMode::WithVat,
            ),
        ));

        self::assertSame(200.0, $result['lineExtensionAmount']);
        self::assertSame(242.0, $result['lineExtensionAmountTaxInclusive']);
        self::assertSame(42.0, $result['taxSubTotal']->getTaxAmount());
    }

    public function test_calculate_respects_custom_rounding_scale(): void
    {
        $calculator = new DocumentLineCalculator(scale: 3);

        $result = $calculator->calculate(new DocumentCalculatedLineInput(
            identity: new DocumentCalculatedLineIdentityInput(
                uuid: 'line-3',
            ),
            lineItem: new IncomingOrderLineItemInput(),
            quantity: new DocumentCalculatedLineQuantityInput(
                value: 1.0,
                unitCode: 'Ks',
            ),
            price: new DocumentCalculatedLinePriceInput(
                unitPrice: 100.555,
                vatRate: 21.0,
                priceMode: DocumentPriceMode::WithoutVat,
            ),
        ));

        self::assertSame(100.555, $result['lineExtensionAmount']);
        self::assertSame(121.672, $result['lineExtensionAmountTaxInclusive']);
        self::assertSame(21.117, $result['taxSubTotal']->getTaxAmount());
    }

    public function test_calculate_throws_when_quantity_is_zero_or_negative(): void
    {
        $calculator = new DocumentLineCalculator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Line quantity must be greater than zero.');

        $this->createInput(
            quantity: 0.0,
            unitPrice: 100.0,
            vatRate: 21.0,
        );
    }

    public function test_calculate_throws_when_unit_price_is_negative(): void
    {
        $calculator = new DocumentLineCalculator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Document unit price must not be negative.');

        $calculator->calculate($this->createInput(
            quantity: 1.0,
            unitPrice: -1.0,
            vatRate: 21.0,
        ));
    }

    public function test_calculate_throws_when_vat_rate_is_negative(): void
    {
        $calculator = new DocumentLineCalculator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Document VAT rate must not be negative.');

        $calculator->calculate($this->createInput(
            quantity: 1.0,
            unitPrice: 100.0,
            vatRate: -21.0,
        ));
    }

    /**
     * @return DocumentCalculatedLineInput<IncomingOrderLineItemInput>
     */
    private function createInput(
        float $quantity,
        float $unitPrice,
        float $vatRate,
    ): DocumentCalculatedLineInput {
        return new DocumentCalculatedLineInput(
            identity: new DocumentCalculatedLineIdentityInput(
                uuid: 'line-test',
            ),
            lineItem: new IncomingOrderLineItemInput(),
            quantity: new DocumentCalculatedLineQuantityInput(
                value: $quantity,
                unitCode: 'Ks',
            ),
            price: new DocumentCalculatedLinePriceInput(
                unitPrice: $unitPrice,
                vatRate: $vatRate,
            ),
        );
    }
}
