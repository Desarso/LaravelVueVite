<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ChecklistOptions extends Enum
{
    const check = 1;
    const radio = 2;
    const text = 3;
    const numeric = 4; 
    const select = 5;
    const header = 6;
}
