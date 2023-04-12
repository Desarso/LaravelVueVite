<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TicketApprover extends Pivot
{
    protected $table = "wh_ticket_approver";
 
}
