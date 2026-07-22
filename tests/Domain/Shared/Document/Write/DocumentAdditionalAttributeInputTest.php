<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Shared\Document\Write;

use InvalidArgumentException;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentTextualAttributeKind;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentAdditionalAttributeInput;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentAdditionalAttributeValueInput;
use PHPUnit\Framework\TestCase;

final class DocumentAdditionalAttributeInputTest extends TestCase
{
    public function test_it_delegates_value_metadata_getters(): void
    {
        $attribute = new DocumentAdditionalAttributeInput(
            name: 'Varianta',
            value: new DocumentAdditionalAttributeValueInput(
                value: 'velka dira',
                langId: 'cs',
                unitCode: 'Ks',
                scheme: DocumentUnitOfMeasureScheme::Unknown,
            ),
            attributeKind: DocumentTextualAttributeKind::FreeText,
        );

        self::assertSame('Varianta', $attribute->getName());
        self::assertSame('velka dira', $attribute->getValue());
        self::assertSame('cs', $attribute->getLangId());
        self::assertSame('Ks', $attribute->getUnitCode());
        self::assertSame(DocumentUnitOfMeasureScheme::Unknown, $attribute->getScheme());
    }

    public function test_it_uses_extended_id_as_default_attribute_kind(): void
    {
        $attribute = new DocumentAdditionalAttributeInput(
            name: 'Varianta',
            value: new DocumentAdditionalAttributeValueInput(
                value: 'velka dira',
            ),
        );

        self::assertSame(DocumentTextualAttributeKind::ExtendedID, $attribute->getAttributeKind());
        self::assertNull($attribute->getLangId());
        self::assertNull($attribute->getUnitCode());
        self::assertNull($attribute->getScheme());
    }

    public function test_it_uses_custom_attribute_kind_when_provided(): void
    {
        $attribute = new DocumentAdditionalAttributeInput(
            name: 'Barva',
            value: new DocumentAdditionalAttributeValueInput(
                value: 'cerna',
            ),
            attributeKind: DocumentTextualAttributeKind::FreeText,
        );

        self::assertSame(DocumentTextualAttributeKind::FreeText, $attribute->getAttributeKind());
    }

    public function test_it_rejects_empty_name(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Additional attribute name must not be empty.');

        new DocumentAdditionalAttributeInput(
            name: '',
            value: new DocumentAdditionalAttributeValueInput(
                value: 'velka dira',
            ),
        );
    }
}
