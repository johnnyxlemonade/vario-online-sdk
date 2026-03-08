<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\ValueObject;

use Lemonade\Vario\Domain\Product\ValueObject\ProductDescription;
use PHPUnit\Framework\TestCase;

final class ProductDescriptionTest extends TestCase
{
    public function testGetters(): void
    {
        $description = new ProductDescription(
            shortDescription: 'Short text',
            description: 'Long description'
        );

        self::assertSame('Short text', $description->getShortDescription());
        self::assertSame('Long description', $description->getDescription());
    }

    public function testNullValues(): void
    {
        $description = new ProductDescription(
            shortDescription: null,
            description: null
        );

        self::assertNull($description->getShortDescription());
        self::assertNull($description->getDescription());
    }

    public function testToArray(): void
    {
        $description = new ProductDescription(
            shortDescription: 'Short text',
            description: 'Long description'
        );

        self::assertSame([
            'shortDescription' => 'Short text',
            'description' => 'Long description',
        ], $description->toArray());
    }
}
