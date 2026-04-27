<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Enum;

use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTextualAttributeKind;
use PHPUnit\Framework\TestCase;

final class IncomingOrderTextualAttributeKindTest extends TestCase
{
    public function test_it_exposes_expected_enum_values(): void
    {
        self::assertSame('FreeText', IncomingOrderTextualAttributeKind::FreeText->value);
        self::assertSame('ExtendedID', IncomingOrderTextualAttributeKind::ExtendedID->value);
        self::assertSame('BarcodeSymbologyID', IncomingOrderTextualAttributeKind::BarcodeSymbologyID->value);
    }

    public function test_to_api_value_returns_backed_value(): void
    {
        self::assertSame(
            'FreeText',
            IncomingOrderTextualAttributeKind::FreeText->toApiValue()
        );

        self::assertSame(
            'ExtendedID',
            IncomingOrderTextualAttributeKind::ExtendedID->toApiValue()
        );

        self::assertSame(
            'BarcodeSymbologyID',
            IncomingOrderTextualAttributeKind::BarcodeSymbologyID->toApiValue()
        );
    }

    public function test_try_from_resolves_known_values(): void
    {
        self::assertSame(
            IncomingOrderTextualAttributeKind::FreeText,
            IncomingOrderTextualAttributeKind::tryFrom('FreeText')
        );

        self::assertSame(
            IncomingOrderTextualAttributeKind::ExtendedID,
            IncomingOrderTextualAttributeKind::tryFrom('ExtendedID')
        );

        self::assertSame(
            IncomingOrderTextualAttributeKind::BarcodeSymbologyID,
            IncomingOrderTextualAttributeKind::tryFrom('BarcodeSymbologyID')
        );
    }

}
