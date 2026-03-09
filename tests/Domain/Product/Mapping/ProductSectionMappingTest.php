<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\Mapping;

use Lemonade\Vario\Domain\Product\Mapping\ProductSectionMapping;
use PHPUnit\Framework\TestCase;

final class ProductSectionMappingTest extends TestCase
{
    public function testMarkerInterfaceCanBeImplemented(): void
    {
        $mapping = new class implements ProductSectionMapping {};

        self::assertInstanceOf(ProductSectionMapping::class, $mapping);
    }
}
