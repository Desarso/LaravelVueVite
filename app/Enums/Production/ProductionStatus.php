<?php

namespace App\Enums\Production;;

use BenSampo\Enum\Enum;

final class ProductionStatus extends Enum
{
    const Pending  = 1;
    const Progress = 2;
    const Paused   = 3;
    const Finished = 4;
}
