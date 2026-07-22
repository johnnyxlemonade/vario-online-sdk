<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\OutgoingQuotation\Write;

use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;

final class OutgoingQuotationPartiesInput
{
    public function __construct(
        private readonly KnownPartyInput $buyerCustomerParty,
        private readonly KnownPartyInput $sellerSupplierParty,
    ) {}

    public function getBuyerCustomerParty(): KnownPartyInput
    {
        return $this->buyerCustomerParty;
    }

    public function getSellerSupplierParty(): KnownPartyInput
    {
        return $this->sellerSupplierParty;
    }
}
