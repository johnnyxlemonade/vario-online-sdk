<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Shared\Document\Write;

use InvalidArgumentException;
use Lemonade\Vario\Domain\Shared\Document\Write\DocumentCalculatedLineIdentityInput;
use PHPUnit\Framework\TestCase;

final class DocumentCalculatedLineIdentityInputTest extends TestCase
{
    public function test_it_rejects_empty_uuid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Line UUID must not be empty.');

        new DocumentCalculatedLineIdentityInput(uuid: '');
    }

    public function test_it_exposes_nullable_id_and_note(): void
    {
        $identity = new DocumentCalculatedLineIdentityInput(
            uuid: 'line-uuid',
            id: null,
            note: null,
        );

        self::assertSame('line-uuid', $identity->getUuid());
        self::assertNull($identity->getId());
        self::assertNull($identity->getNote());
    }
}
