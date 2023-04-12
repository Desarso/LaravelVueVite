<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TicketStatus extends Enum
{
    const Pending  = 1;
    const Progress = 2;
    const Paused   = 3;
    const Finished = 4;
}
