<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class LogAction extends Enum
{
    const Login              = 1;
    const CreateTicket       = 2;
    const EditTicket         = 3;
    const DeleteTicket       = 4;
    const User               = 5;
    const CreateNote         = 6;
    const DeleteNote         = 7;
    const Tag                = 8;
    const Copy               = 9;
    const Approver           = 10;
}
