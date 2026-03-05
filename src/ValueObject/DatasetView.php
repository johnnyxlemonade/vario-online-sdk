<?php

declare(strict_types=1);

namespace Lemonade\Vario\ValueObject;

enum DatasetView: string implements DatasetViewInterface
{
    use DatasetViewBehaviour;

    case KATALOG_ALL = 'Katalog/ALL';
    case KATALOG_STROM = 'Katalog/StromAPI';
    case KATALOG_STROM_PRODUKTY = 'Katalog/StromProduktyAPI';

    protected function rawValue(): string
    {
        return $this->value;
    }
}
