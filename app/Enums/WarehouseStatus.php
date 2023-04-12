<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class WarehouseStatus extends Enum
{
    const Pending  = 1;
    const Received = 2;
    const Rejected = 3;
    const GeneratedOrder = 4;
    const Finished = 5;
}
