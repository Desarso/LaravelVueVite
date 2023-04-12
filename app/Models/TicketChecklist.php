<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TicketChecklist extends Model
{
    protected $table = "wh_ticket_checklist";

    protected $fillable = ["idticket", "idchecklist", "options", "results", "idevaluator"];

    //Relationships
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'idticket', 'id');
    }

    public function checklist()
    {
        return $this->belongsTo(Checklist::class, 'idchecklist', 'id');
    }
}
