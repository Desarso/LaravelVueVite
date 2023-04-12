<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ReminderType extends Enum
{
    const Duedate  = 1;
    const ByClient = 2;
}
