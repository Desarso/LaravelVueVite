<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingUpdate extends Model
{
    protected $table = "wh_setting_update";
    protected $fillable =  ["name"];
}
