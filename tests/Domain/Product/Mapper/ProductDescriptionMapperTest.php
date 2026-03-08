<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapper\ProductDescriptionMapper;
use Lemonade\Vario\Domain\Product\Mapping\ProductDescriptionMapping;
use Lemonade\Vario\Domain\Product\ValueObject\ProductDescription;
use PHPUnit\Framework\TestCase;

final class ProductDescriptionMapperTest extends TestCase
{
    public function testMapsDescription(): void
    {
        $mapping = new ProductDescriptionMapping(
            shortDescription: 'short',
            description: 'description'
        );

        $row = new DatasetRow([
            'short' => 'Short text',
            'description' => 'Long description',
        ]);

        $mapper = new ProductDescriptionMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductDescription::class, $result);
        self::assertSame('Short text', $result->getShortDescription());
        self::assertSame('Long description', $result->getDescription());
    }

    public function testReturnsNullWhenBothFieldsMissing(): void
    {
        $mapping = new ProductDescriptionMapping(
            shortDescription: 'short',
            description: 'description'
        );

        $row = new DatasetRow([]);

        $mapper = new ProductDescriptionMapper($mapping);

        self::assertNull($mapper->map($row));
    }

    public function testMapsPartialDescription(): void
    {
        $mapping = new ProductDescriptionMapping(
            shortDescription: 'short',
            description: 'description'
        );

        $row = new DatasetRow([
            'short' => 'Short text',
        ]);

        $mapper = new ProductDescriptionMapper($mapping);

        $result = $mapper->map($row);

        self::assertInstanceOf(ProductDescription::class, $result);
        self::assertSame('Short text', $result->getShortDescription());
        self::assertNull($result->getDescription());
    }
}
