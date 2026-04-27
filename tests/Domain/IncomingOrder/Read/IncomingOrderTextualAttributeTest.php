<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Read;

use Lemonade\Vario\Domain\IncomingOrder\Enum\IncomingOrderTextualAttributeKind;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderTextualAttribute;
use PHPUnit\Framework\TestCase;

final class IncomingOrderTextualAttributeTest extends TestCase
{
    public function test_it_exposes_all_values(): void
    {
        $attribute = new IncomingOrderTextualAttribute(
            attributeKind: IncomingOrderTextualAttributeKind::ExtendedID,
            name: 'Cislo_dokladu',
            value: 'D-25-02102',
            langId: 'cs',
            extra: [
                'Source' => 'API',
            ],
        );

        self::assertSame(
            IncomingOrderTextualAttributeKind::ExtendedID,
            $attribute->getAttributeKind()
        );
        self::assertSame('Cislo_dokladu', $attribute->getName());
        self::assertSame('D-25-02102', $attribute->getValue());
        self::assertSame('cs', $attribute->getLangId());
        self::assertSame([
            'Source' => 'API',
        ], $attribute->getExtra());
    }

    public function test_it_supports_nullable_fields(): void
    {
        $attribute = new IncomingOrderTextualAttribute(
            attributeKind: IncomingOrderTextualAttributeKind::FreeText,
        );

        self::assertSame(
            IncomingOrderTextualAttributeKind::FreeText,
            $attribute->getAttributeKind()
        );
        self::assertNull($attribute->getName());
        self::assertNull($attribute->getValue());
        self::assertNull($attribute->getLangId());
        self::assertSame([], $attribute->getExtra());
    }
}
