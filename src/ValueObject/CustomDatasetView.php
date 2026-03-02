<?php declare(strict_types=1);

namespace Lemonade\Vario\ValueObject;

final class CustomDatasetView implements DatasetViewInterface
{
    use DatasetViewBehaviour;

    public function __construct(
        private readonly string $value
    ) {}

    protected function rawValue(): string
    {
        return $this->value;
    }
}
