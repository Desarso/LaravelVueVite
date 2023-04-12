<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSchedule extends Model
{
    protected $table = "wh_user_schedule";

    protected $fillable =  ["iduser", "idshift", "date"];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'iduser');
    }

    public function shift()
    {
        return $this->hasOne(Shift::class, 'id', 'idshift');
    }
}
