<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Shared;

use Lemonade\Vario\Domain\Shared\IdentificationScheme;
use PHPUnit\Framework\TestCase;

final class IdentificationSchemeTest extends TestCase
{
    public function test_api_values(): void
    {
        self::assertSame(0, IdentificationScheme::UIN->toApiValue());
        self::assertSame(1, IdentificationScheme::VAT->toApiValue());
        self::assertSame(2, IdentificationScheme::GLN->toApiValue());
        self::assertSame(3, IdentificationScheme::BIC->toApiValue());
        self::assertSame(90, IdentificationScheme::UUID->toApiValue());
        self::assertSame(91, IdentificationScheme::ISID->toApiValue());
        self::assertSame(99, IdentificationScheme::OTHER->toApiValue());
    }
}
