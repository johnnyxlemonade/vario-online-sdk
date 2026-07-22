<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Read;

use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderUnitPrice;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;
use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentQuantity;
use PHPUnit\Framework\TestCase;

final class IncomingOrderUnitPriceTest extends TestCase
{
    public function test_it_exposes_all_values(): void
    {
        $quantity = new DocumentQuantity(
            value: 1.5,
            unitCode: 'Ks',
            scheme: DocumentUnitOfMeasureScheme::Unknown,
        );

        $price = new IncomingOrderUnitPrice(
            amount: 100.5,
            amountTaxInclusive: 121.61,
            quantity: $quantity,
            extra: [
                'PriceListReference' => [
                    'IssuerDocumentID' => 'ABC123',
                ],
            ],
        );

        self::assertSame(100.5, $price->getAmount());
        self::assertSame(121.61, $price->getAmountTaxInclusive());
        self::assertSame($quantity, $price->getQuantity());
        self::assertSame([
            'PriceListReference' => [
                'IssuerDocumentID' => 'ABC123',
            ],
        ], $price->getExtra());
    }

    public function test_it_supports_empty_state(): void
    {
        $price = new IncomingOrderUnitPrice();

        self::assertNull($price->getAmount());
        self::assertNull($price->getAmountTaxInclusive());
        self::assertNull($price->getQuantity());
        self::assertSame([], $price->getExtra());
    }
}
