<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Session;

class Log extends Model
{
    protected $table = "wh_log";

    protected $fillable =  ["action", "data", "idstatus", "idticket", "iduser", "uuid", "location", "created_at"];

    //Relationships
    public function user()
    {
        return $this->hasOne(User::class,'id','iduser');
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class,'id','idticket')->withTrashed();
    }

    public function getCreatedAtAttribute($value) 
    {     
        return Carbon::parse($value)->setTimezone('America/Costa_Rica');
    }
}
