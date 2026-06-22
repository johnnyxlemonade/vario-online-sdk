<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Write;

use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTextualAttributeKind;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentAdditionalAttributeInput;
use PHPUnit\Framework\TestCase;

final class IncomingOrderAdditionalAttributeInputTest extends TestCase
{
    public function test_it_exposes_all_values(): void
    {
        $attribute = new DocumentAdditionalAttributeInput(
            name: 'Varianta',
            value: 'velká díra',
            attributeKind: DocumentTextualAttributeKind::FreeText,
            langId: 'cs',
            unitCode: 'Ks',
            scheme: DocumentUnitOfMeasureScheme::Unknown,
        );

        self::assertSame('Varianta', $attribute->getName());
        self::assertSame('velká díra', $attribute->getValue());
        self::assertSame(
            DocumentTextualAttributeKind::FreeText,
            $attribute->getAttributeKind()
        );
        self::assertSame('cs', $attribute->getLangId());
        self::assertSame('Ks', $attribute->getUnitCode());
        self::assertSame(
            DocumentUnitOfMeasureScheme::Unknown,
            $attribute->getScheme()
        );
    }

    public function test_it_uses_expected_defaults(): void
    {
        $attribute = new DocumentAdditionalAttributeInput(
            name: 'Varianta',
            value: 'velká díra',
        );

        self::assertSame('Varianta', $attribute->getName());
        self::assertSame('velká díra', $attribute->getValue());
        self::assertSame(
            DocumentTextualAttributeKind::ExtendedID,
            $attribute->getAttributeKind()
        );
        self::assertNull($attribute->getLangId());
        self::assertNull($attribute->getUnitCode());
        self::assertNull($attribute->getScheme());
    }
}
