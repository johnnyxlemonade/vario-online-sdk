<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Shared\Document\Write;

use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxCalculationMethod;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTaxScheme;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineTaxInput;
use PHPUnit\Framework\TestCase;

final class DocumentCalculatedLineTaxInputTest extends TestCase
{
    public function test_it_uses_expected_defaults(): void
    {
        $tax = new DocumentCalculatedLineTaxInput();

        self::assertSame(DocumentTaxCalculationMethod::Add, $tax->getCalculationMethod());
        self::assertSame(DocumentTaxScheme::Vat, $tax->getScheme());
        self::assertNull($tax->getSchemeExtensionCode());
    }
}
