<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $table = "wh_reminder";

    protected $fillable = ['idticket', 'sent', 'type', 'notify_at'];

    public function ticket()
    {
        return $this->hasOne(Ticket::class,'id','idticket');
    }
}
