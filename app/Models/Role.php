<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;
    protected $table = "wh_role";
    protected $fillable =  ["name", "permissions"];

    //Relationships
    function users()
    {
        return $this->hasMany(UserTeam::class, 'idrole', 'id');
    }
}
