<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    protected $table = "wh_filter";
    protected $fillable =  ["name", "data", "iduser"];
}
