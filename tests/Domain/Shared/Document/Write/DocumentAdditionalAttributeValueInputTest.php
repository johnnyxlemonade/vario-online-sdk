<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Shared\Document\Write;

use InvalidArgumentException;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentAdditionalAttributeValueInput;
use PHPUnit\Framework\TestCase;

final class DocumentAdditionalAttributeValueInputTest extends TestCase
{
    public function test_it_rejects_empty_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Additional attribute value must not be empty.');

        new DocumentAdditionalAttributeValueInput(value: '');
    }

    public function test_it_exposes_nullable_lang_and_unit_fields(): void
    {
        $value = new DocumentAdditionalAttributeValueInput(
            value: 'cerna',
            langId: null,
            unitCode: null,
            scheme: null,
        );

        self::assertSame('cerna', $value->getValue());
        self::assertNull($value->getLangId());
        self::assertNull($value->getUnitCode());
        self::assertNull($value->getScheme());
    }

    public function test_it_exposes_all_fields(): void
    {
        $value = new DocumentAdditionalAttributeValueInput(
            value: 'baleni',
            langId: 'cs',
            unitCode: 'bal',
            scheme: DocumentUnitOfMeasureScheme::SI,
        );

        self::assertSame('baleni', $value->getValue());
        self::assertSame('cs', $value->getLangId());
        self::assertSame('bal', $value->getUnitCode());
        self::assertSame(DocumentUnitOfMeasureScheme::SI, $value->getScheme());
    }
}
