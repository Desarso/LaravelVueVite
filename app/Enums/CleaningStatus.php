<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class CleaningStatus extends Enum
{
    const Dirty    = 1;
    const Cleaning = 2;
    const Paused   = 3;
    const Clean    = 4;
    const Inspected = 5;
    const Rush = 7;
}
