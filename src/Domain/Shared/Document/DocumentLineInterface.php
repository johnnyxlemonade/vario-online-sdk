<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\Shared\Document;

use Lemonade\Vario\Domain\Shared\Document\ValueObject\DocumentTaxSubTotal;

interface DocumentLineInterface
{
    public function getLineExtensionAmount(): float;

    public function getLineExtensionAmountTaxInclusive(): float;

    public function getTaxSubTotal(): DocumentTaxSubTotal;
}
