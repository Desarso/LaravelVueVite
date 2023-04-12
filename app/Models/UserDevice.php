<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    protected $table = "wh_user_device";

    protected $fillable =  ["iduser", "os", "token"];
}
