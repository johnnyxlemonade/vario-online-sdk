<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Shared\Document\Write;

use InvalidArgumentException;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentLineIdentityInput;
use PHPUnit\Framework\TestCase;

final class DocumentLineIdentityInputTest extends TestCase
{
    public function test_it_exposes_values(): void
    {
        $identity = new DocumentLineIdentityInput(
            uuid: 'line-uuid',
            id: 'ROW-1',
            note: 'Line note',
        );

        self::assertSame('line-uuid', $identity->getUuid());
        self::assertSame('ROW-1', $identity->getId());
        self::assertSame('Line note', $identity->getNote());
    }

    public function test_it_rejects_empty_uuid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Line UUID must not be empty.');

        new DocumentLineIdentityInput('');
    }
}
