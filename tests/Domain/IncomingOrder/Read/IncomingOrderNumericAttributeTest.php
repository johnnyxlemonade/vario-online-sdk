<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Read;

use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderNumericAttributeKind;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderNumericAttribute;
use PHPUnit\Framework\TestCase;

final class IncomingOrderNumericAttributeTest extends TestCase
{
    public function test_it_exposes_all_values(): void
    {
        $attribute = new IncomingOrderNumericAttribute(
            attributeKind: IncomingOrderNumericAttributeKind::FreeNumeric,
            name: 'Pocet_polozky_1',
            value: 5.0,
            unitCode: 'Ks',
            extra: [
                'Source' => 'API',
            ],
        );

        self::assertSame(
            IncomingOrderNumericAttributeKind::FreeNumeric,
            $attribute->getAttributeKind(),
        );
        self::assertSame('Pocet_polozky_1', $attribute->getName());
        self::assertSame(5.0, $attribute->getValue());
        self::assertSame('Ks', $attribute->getUnitCode());
        self::assertSame([
            'Source' => 'API',
        ], $attribute->getExtra());
    }

    public function test_it_supports_nullable_fields(): void
    {
        $attribute = new IncomingOrderNumericAttribute(
            attributeKind: IncomingOrderNumericAttributeKind::MeasurementDimension,
        );

        self::assertSame(
            IncomingOrderNumericAttributeKind::MeasurementDimension,
            $attribute->getAttributeKind(),
        );
        self::assertNull($attribute->getName());
        self::assertNull($attribute->getValue());
        self::assertNull($attribute->getUnitCode());
        self::assertSame([], $attribute->getExtra());
    }
}
