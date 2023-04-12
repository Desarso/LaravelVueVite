<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class WarehouseLogAction extends Enum
{
    const Create       = 1;
    const Edit         = 2;
    const Delete       = 3;
    const ChangeStatus = 4;
}
