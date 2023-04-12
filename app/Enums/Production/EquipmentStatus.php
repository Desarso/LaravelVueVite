<?php

namespace App\Enums\Production;

use BenSampo\Enum\Enum;

final class EquipmentStatus extends Enum
{
    const Off  = 1;
    const Working = 2;
    const Stopped   = 3;
   
}
