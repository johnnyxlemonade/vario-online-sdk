<?php

declare(strict_types=1);

namespace Lemonade\Vario\Tests\Domain\Product\Mapper;

use Lemonade\Vario\Domain\Product\DatasetRow;
use Lemonade\Vario\Domain\Product\Mapper\AbstractProductSectionMapper;
use Lemonade\Vario\Domain\Product\ValueObject\ProductSection;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class AbstractProductSectionMapperTest extends TestCase
{
    private function mapper(): TestProductSectionMapper
    {
        return new TestProductSectionMapper();
    }

    public function testMapBoolReturnsTrue(): void
    {
        $row = new DatasetRow(['flag' => 1]);
        $mapper = $this->mapper();

        self::assertTrue($mapper->callMapBool($row, 'flag'));
    }

    public function testMapBoolReturnsNullWhenColumnNull(): void
    {
        $row = new DatasetRow(['flag' => 1]);
        $mapper = $this->mapper();

        self::assertNull($mapper->callMapBool($row, null));
    }

    public function testMapBoolReturnsNullWhenValueMissing(): void
    {
        $row = new DatasetRow([]);
        $mapper = $this->mapper();

        self::assertNull($mapper->callMapBool($row, 'flag'));
    }

    public function testMapBoolReturnsNullWhenEmptyString(): void
    {
        $row = new DatasetRow(['flag' => '']);
        $mapper = $this->mapper();

        self::assertNull($mapper->callMapBool($row, 'flag'));
    }

    public function testRequireStringReturnsValue(): void
    {
        $row = new DatasetRow(['name' => 'Product']);
        $mapper = $this->mapper();

        self::assertSame('Product', $mapper->callRequireString($row, 'name'));
    }

    public function testRequireStringThrowsException(): void
    {
        $this->expectException(RuntimeException::class);

        $row = new DatasetRow([]);
        $mapper = $this->mapper();

        $mapper->callRequireString($row, 'name');
    }
}

/**
 * @extends AbstractProductSectionMapper<ProductSection>
 */
final class TestProductSectionMapper extends AbstractProductSectionMapper
{
    public function map(DatasetRow $row): ?ProductSection
    {
        return null;
    }

    public function callMapBool(DatasetRow $row, ?string $column): ?bool
    {
        return $this->mapBool($row, $column);
    }

    public function callRequireString(DatasetRow $row, ?string $column): string
    {
        return $this->requireString($row, $column);
    }
}
