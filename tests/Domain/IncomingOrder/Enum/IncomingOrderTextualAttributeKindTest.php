<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Enum;

use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTextualAttributeKind;
use PHPUnit\Framework\TestCase;

final class IncomingOrderTextualAttributeKindTest extends TestCase
{
    public function test_it_exposes_expected_enum_values(): void
    {
        self::assertSame('FreeText', DocumentTextualAttributeKind::FreeText->value);
        self::assertSame('ExtendedID', DocumentTextualAttributeKind::ExtendedID->value);
        self::assertSame('BarcodeSymbologyID', DocumentTextualAttributeKind::BarcodeSymbologyID->value);
    }

    public function test_to_api_value_returns_backed_value(): void
    {
        self::assertSame(
            'FreeText',
            DocumentTextualAttributeKind::FreeText->toApiValue()
        );

        self::assertSame(
            'ExtendedID',
            DocumentTextualAttributeKind::ExtendedID->toApiValue()
        );

        self::assertSame(
            'BarcodeSymbologyID',
            DocumentTextualAttributeKind::BarcodeSymbologyID->toApiValue()
        );
    }

    public function test_try_from_resolves_known_values(): void
    {
        self::assertSame(
            DocumentTextualAttributeKind::FreeText,
            DocumentTextualAttributeKind::tryFrom('FreeText')
        );

        self::assertSame(
            DocumentTextualAttributeKind::ExtendedID,
            DocumentTextualAttributeKind::tryFrom('ExtendedID')
        );

        self::assertSame(
            DocumentTextualAttributeKind::BarcodeSymbologyID,
            DocumentTextualAttributeKind::tryFrom('BarcodeSymbologyID')
        );
    }

}
