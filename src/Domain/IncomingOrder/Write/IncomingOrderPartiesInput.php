<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Write;

use Lemonade\Vario\Domain\KnownParty\KnownPartyInput;

final class IncomingOrderPartiesInput
{
    public function __construct(
        private readonly KnownPartyInput $buyerCustomerParty,
        private readonly KnownPartyInput $sellerSupplierParty,
        private readonly ?KnownPartyInput $accountingCustomerParty = null,
        private readonly ?KnownPartyInput $delivery = null,
    ) {}

    public function getBuyerCustomerParty(): KnownPartyInput
    {
        return $this->buyerCustomerParty;
    }

    public function getSellerSupplierParty(): KnownPartyInput
    {
        return $this->sellerSupplierParty;
    }

    public function getAccountingCustomerParty(): ?KnownPartyInput
    {
        return $this->accountingCustomerParty;
    }

    public function getDelivery(): ?KnownPartyInput
    {
        return $this->delivery;
    }
}
