<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\IncomingOrder\Read;

use DateTimeImmutable;
use Lemonade\Vario\Domain\IncomingOrder\Read\IncomingOrderDeliveryDetail;
use PHPUnit\Framework\TestCase;

final class IncomingOrderDeliveryDetailTest extends TestCase
{
    public function test_it_exposes_all_values(): void
    {
        $requestedDeliveryDate = new DateTimeImmutable('2025-05-06T00:00:00+01:00');

        $detail = new IncomingOrderDeliveryDetail(
            optionCodes: [
                'raben 7.5.2025',
                'liftgate',
            ],
            requestedDeliveryDate: $requestedDeliveryDate,
            extra: [
                'CarrierNote' => 'Call before delivery',
            ],
        );

        self::assertSame(
            ['raben 7.5.2025', 'liftgate'],
            $detail->getOptionCodes(),
        );
        self::assertTrue($detail->hasOptionCodes());
        self::assertSame($requestedDeliveryDate, $detail->getRequestedDeliveryDate());
        self::assertSame([
            'CarrierNote' => 'Call before delivery',
        ], $detail->getExtra());
    }

    public function test_it_supports_empty_state(): void
    {
        $detail = new IncomingOrderDeliveryDetail();

        self::assertSame([], $detail->getOptionCodes());
        self::assertFalse($detail->hasOptionCodes());
        self::assertNull($detail->getRequestedDeliveryDate());
        self::assertSame([], $detail->getExtra());
    }
}
