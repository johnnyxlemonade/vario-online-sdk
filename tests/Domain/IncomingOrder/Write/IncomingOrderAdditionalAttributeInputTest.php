<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Write;

use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTextualAttributeKind;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderUnitOfMeasureScheme;
use Lemonade\Vario\Domain\IncomingOrder\Write\IncomingOrderAdditionalAttributeInput;
use PHPUnit\Framework\TestCase;

final class IncomingOrderAdditionalAttributeInputTest extends TestCase
{
    public function test_it_exposes_all_values(): void
    {
        $attribute = new IncomingOrderAdditionalAttributeInput(
            name: 'Varianta',
            value: 'velká díra',
            attributeKind: IncomingOrderTextualAttributeKind::FreeText,
            langId: 'cs',
            unitCode: 'Ks',
            scheme: IncomingOrderUnitOfMeasureScheme::Unknown,
        );

        self::assertSame('Varianta', $attribute->getName());
        self::assertSame('velká díra', $attribute->getValue());
        self::assertSame(
            IncomingOrderTextualAttributeKind::FreeText,
            $attribute->getAttributeKind()
        );
        self::assertSame('cs', $attribute->getLangId());
        self::assertSame('Ks', $attribute->getUnitCode());
        self::assertSame(
            IncomingOrderUnitOfMeasureScheme::Unknown,
            $attribute->getScheme()
        );
    }

    public function test_it_uses_expected_defaults(): void
    {
        $attribute = new IncomingOrderAdditionalAttributeInput(
            name: 'Varianta',
            value: 'velká díra',
        );

        self::assertSame('Varianta', $attribute->getName());
        self::assertSame('velká díra', $attribute->getValue());
        self::assertSame(
            IncomingOrderTextualAttributeKind::ExtendedID,
            $attribute->getAttributeKind()
        );
        self::assertNull($attribute->getLangId());
        self::assertNull($attribute->getUnitCode());
        self::assertNull($attribute->getScheme());
    }
}
