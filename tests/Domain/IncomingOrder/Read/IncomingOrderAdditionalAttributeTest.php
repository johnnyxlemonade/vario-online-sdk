<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Read;

use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTextualAttributeKind;
use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderUnitOfMeasureScheme;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderAdditionalAttribute;
use PHPUnit\Framework\TestCase;

final class IncomingOrderAdditionalAttributeTest extends TestCase
{
    public function test_it_exposes_all_values(): void
    {
        $attribute = new IncomingOrderAdditionalAttribute(
            attributeKind: IncomingOrderTextualAttributeKind::ExtendedID,
            name: 'Varianta',
            value: 'velká díra',
            langId: 'cs',
            unitCode: 'Ks',
            scheme: IncomingOrderUnitOfMeasureScheme::Unknown,
            extra: [
                'Source' => 'API',
            ],
        );

        self::assertSame(
            IncomingOrderTextualAttributeKind::ExtendedID,
            $attribute->getAttributeKind()
        );
        self::assertSame('Varianta', $attribute->getName());
        self::assertSame('velká díra', $attribute->getValue());
        self::assertSame('cs', $attribute->getLangId());
        self::assertSame('Ks', $attribute->getUnitCode());
        self::assertSame(
            IncomingOrderUnitOfMeasureScheme::Unknown,
            $attribute->getScheme()
        );
        self::assertSame([
            'Source' => 'API',
        ], $attribute->getExtra());
    }

    public function test_it_supports_nullable_optional_fields(): void
    {
        $attribute = new IncomingOrderAdditionalAttribute(
            attributeKind: IncomingOrderTextualAttributeKind::FreeText,
            name: 'Barva',
            value: 'černá',
        );

        self::assertSame(
            IncomingOrderTextualAttributeKind::FreeText,
            $attribute->getAttributeKind()
        );
        self::assertSame('Barva', $attribute->getName());
        self::assertSame('černá', $attribute->getValue());
        self::assertNull($attribute->getLangId());
        self::assertNull($attribute->getUnitCode());
        self::assertNull($attribute->getScheme());
        self::assertSame([], $attribute->getExtra());
    }
}
