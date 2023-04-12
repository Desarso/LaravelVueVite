<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = "wh_notification";

    protected $fillable =  ["title", "message", "idreference", "type", "read"];

    //Relationships
    public function users()
    {
        return $this->belongsToMany(User::class, 'wh_notification_user', 'idnotification', 'iduser');
    }
}
