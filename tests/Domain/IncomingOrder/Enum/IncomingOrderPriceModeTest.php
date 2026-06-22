<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Enum;

use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentPriceMode;
use PHPUnit\Framework\TestCase;

final class IncomingOrderPriceModeTest extends TestCase
{
    public function test_it_exposes_expected_enum_values(): void
    {
        self::assertSame('WithoutVat', DocumentPriceMode::WithoutVat->value);
        self::assertSame('WithVat', DocumentPriceMode::WithVat->value);
    }

    public function test_to_api_value_returns_backed_value(): void
    {
        self::assertSame('WithoutVat', DocumentPriceMode::WithoutVat->toApiValue());
        self::assertSame('WithVat', DocumentPriceMode::WithVat->toApiValue());
    }

    public function test_try_from_maps_known_values(): void
    {
        self::assertSame(
            DocumentPriceMode::WithoutVat,
            DocumentPriceMode::tryFrom('WithoutVat')
        );

        self::assertSame(
            DocumentPriceMode::WithVat,
            DocumentPriceMode::tryFrom('WithVat')
        );
    }

}
