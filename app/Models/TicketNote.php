<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use Carbon\Carbon;

class TicketNote extends Model
{
    protected $table = "wh_ticket_note";

    protected $fillable = ["idticket", "uuid", "note", "type", "created_by", "idchecklistoption", "created_at"];

    //Relationships
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'idticket', 'id');
    }
    
    public function createdBy()
    {
        return $this->hasOne(User::class, 'id','created_by')->select(['id', 'firstname', 'lastname', 'urlpicture']);
    }

    public function getCreatedAtAttribute($value) 
    {     
        return Carbon::parse($value)->setTimezone(Session::get('local_timezone'));
    }
}
