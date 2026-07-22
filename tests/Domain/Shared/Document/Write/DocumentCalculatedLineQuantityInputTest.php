<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Shared\Document\Write;

use InvalidArgumentException;
use Lemonade\Vario\Domain\Shared\Document\Enum\DocumentUnitOfMeasureScheme;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineQuantityInput;
use PHPUnit\Framework\TestCase;

final class DocumentCalculatedLineQuantityInputTest extends TestCase
{
    public function test_it_rejects_zero_or_negative_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Line quantity must be greater than zero.');

        new DocumentCalculatedLineQuantityInput(
            value: 0.0,
            unitCode: 'Ks',
        );
    }

    public function test_it_rejects_empty_unit_code(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Line quantity unit code must not be empty.');

        new DocumentCalculatedLineQuantityInput(
            value: 1.0,
            unitCode: '',
        );
    }

    public function test_it_exposes_value_unit_code_and_scheme(): void
    {
        $quantity = new DocumentCalculatedLineQuantityInput(
            value: 2.5,
            unitCode: 'm2',
            scheme: DocumentUnitOfMeasureScheme::SI,
        );

        self::assertSame(2.5, $quantity->getValue());
        self::assertSame('m2', $quantity->getUnitCode());
        self::assertSame(DocumentUnitOfMeasureScheme::SI, $quantity->getScheme());
    }
}
