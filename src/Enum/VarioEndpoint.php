<?php declare(strict_types=1);

namespace Lemonade\Vario\Enum;

enum VarioEndpoint: string
{
    case IncomingOrder   = '/openapi/IncomingOrder';
    case OutgoingInvoice = '/openapi/OutgoingInvoice';
    case KnownParty      = '/openapi/KnownParty';
    case DatasetView     = '/Api/GetDatasetView';
}
