<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Enum;

use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderPriceMode;
use PHPUnit\Framework\TestCase;

final class IncomingOrderPriceModeTest extends TestCase
{
    public function test_it_exposes_expected_enum_values(): void
    {
        self::assertSame('WithoutVat', IncomingOrderPriceMode::WithoutVat->value);
        self::assertSame('WithVat', IncomingOrderPriceMode::WithVat->value);
    }

    public function test_to_api_value_returns_backed_value(): void
    {
        self::assertSame('WithoutVat', IncomingOrderPriceMode::WithoutVat->toApiValue());
        self::assertSame('WithVat', IncomingOrderPriceMode::WithVat->toApiValue());
    }

    public function test_try_from_maps_known_values(): void
    {
        self::assertSame(
            IncomingOrderPriceMode::WithoutVat,
            IncomingOrderPriceMode::tryFrom('WithoutVat')
        );

        self::assertSame(
            IncomingOrderPriceMode::WithVat,
            IncomingOrderPriceMode::tryFrom('WithVat')
        );
    }

}
