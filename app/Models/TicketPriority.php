<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketPriority extends Model
{
    protected $table = "wh_ticket_priority";

    protected $fillable = ["sla"];
}
