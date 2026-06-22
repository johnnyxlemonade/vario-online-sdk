<?php

declare(strict_types=1);

namespace Lemonade\Vario\Enum;

enum VarioEndpoint: string
{
    case IncomingOrder = '/openapi/IncomingOrder';
    case OutgoingQuotation = '/openapi/OutgoingQuotation';
    case KnownParty = '/openapi/KnownParty';
    case DatasetView = '/Api/GetDatasetView';
}
