<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = "wh_menu";

    protected $fillable =  ["name", "type", "url", "icon", "idparent", "position", "enable"];

    function submenu()
    {
        return $this->hasMany(Menu::class, 'idparent', 'id');
    }

    public function setEnableAttribute($value)
    {          
        $value == "true" ? $this->attributes['enable'] = 1 : $this->attributes['enable'] = 0;         
    }
}
