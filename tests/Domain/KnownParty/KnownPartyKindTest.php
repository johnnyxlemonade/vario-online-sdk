<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\KnownParty;

use Lemonade\Vario\Domain\KnownParty\KnownPartyKind;
use PHPUnit\Framework\TestCase;

final class KnownPartyKindTest extends TestCase
{
    public function testApiValues(): void
    {
        self::assertSame(0, KnownPartyKind::Organization->toApiValue());
        self::assertSame(1, KnownPartyKind::OrganizationBranch->toApiValue());
        self::assertSame(2, KnownPartyKind::Entrepreneur->toApiValue());
        self::assertSame(3, KnownPartyKind::Person->toApiValue());
    }

    public function testEnumValues(): void
    {
        self::assertSame('Organization', KnownPartyKind::Organization->value);
        self::assertSame('OrganizationBranch', KnownPartyKind::OrganizationBranch->value);
        self::assertSame('Entrepreneur', KnownPartyKind::Entrepreneur->value);
        self::assertSame('Person', KnownPartyKind::Person->value);
    }
}
