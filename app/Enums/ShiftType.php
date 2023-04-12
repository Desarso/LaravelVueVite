<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ShiftType extends Enum
{
    const Day           = 'DAY';
    const Night         = 'NIGHT';
    const Mix           = 'MIX';
    const DayOff        = 'DAY_OFF';
}
