<?php

declare(strict_types=1);

namespace Lemonade\Vario\Domain\IncomingOrder\Enum;

/**
 * Class IncomingOrderPaymentMeansCode
 *
 * Supported payment means codes for IncomingOrder write payloads.
 *
 * These values follow the string representation used by the
 * real Vario IncomingOrder API examples and support payloads.
 *
 * @package     Lemonade Framework
 * @subpackage  Lemonade\Vario\Domain\IncomingOrder
 * @link        https://lemonadeframework.cz/
 * @author      Honza Mudrak <honzamudrak@gmail.com>
 * @license     MIT
 * @since       1.0
 */
enum IncomingOrderPaymentMeansCode: string
{
    case Cash = 'Cash';
    case Cheque = 'Cheque';
    case BankAccount = 'BankAccount';
    case BankCard = 'BankCard';
    case DirectDebit = 'DirectDebit';
    case CashOnDelivery = 'CashOnDelivery';
    case Clearing = 'Clearing';

    public function toApiValue(): string
    {
        return $this->value;
    }
}
