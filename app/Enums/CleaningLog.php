<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class CleaningLog extends Enum
{
    const CreatePlan = 1;
    const EditPlan   = 2;
    const DeletePlan = 3;
    const EditSpot   = 4;
}
