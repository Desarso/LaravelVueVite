<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes;
    protected $table = "wh_team";
    protected $fillable = ['id', 'name', 'description', 'color', 'emails', 'bosses'];
    protected $hidden = ['pivot'];

    //Relationships
    public function users()
    {
        return $this->belongsToMany(User::class, 'wh_user_team', 'idteam', 'iduser');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'idteam', 'id');
    }
}
