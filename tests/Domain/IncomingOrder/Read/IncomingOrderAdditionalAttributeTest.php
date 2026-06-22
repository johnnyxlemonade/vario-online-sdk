<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Read;

use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTextualAttributeKind;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderAdditionalAttribute;
use PHPUnit\Framework\TestCase;

final class IncomingOrderAdditionalAttributeTest extends TestCase
{
    public function test_it_exposes_all_values(): void
    {
        $attribute = new IncomingOrderAdditionalAttribute(
            attributeKind: DocumentTextualAttributeKind::ExtendedID,
            name: 'Varianta',
            value: 'velkÄ‚Ë‡ dÄ‚Â­ra',
            langId: 'cs',
            unitCode: 'Ks',
            scheme: DocumentUnitOfMeasureScheme::Unknown,
            extra: [
                'Source' => 'API',
            ],
        );

        self::assertSame(
            DocumentTextualAttributeKind::ExtendedID,
            $attribute->getAttributeKind()
        );
        self::assertSame('Varianta', $attribute->getName());
        self::assertSame('velkÄ‚Ë‡ dÄ‚Â­ra', $attribute->getValue());
        self::assertSame('cs', $attribute->getLangId());
        self::assertSame('Ks', $attribute->getUnitCode());
        self::assertSame(
            DocumentUnitOfMeasureScheme::Unknown,
            $attribute->getScheme()
        );
        self::assertSame([
            'Source' => 'API',
        ], $attribute->getExtra());
    }

    public function test_it_supports_nullable_optional_fields(): void
    {
        $attribute = new IncomingOrderAdditionalAttribute(
            attributeKind: DocumentTextualAttributeKind::FreeText,
            name: 'Barva',
            value: 'Ă„Ĺ¤ernÄ‚Ë‡',
        );

        self::assertSame(
            DocumentTextualAttributeKind::FreeText,
            $attribute->getAttributeKind()
        );
        self::assertSame('Barva', $attribute->getName());
        self::assertSame('Ă„Ĺ¤ernÄ‚Ë‡', $attribute->getValue());
        self::assertNull($attribute->getLangId());
        self::assertNull($attribute->getUnitCode());
        self::assertNull($attribute->getScheme());
        self::assertSame([], $attribute->getExtra());
    }
}
