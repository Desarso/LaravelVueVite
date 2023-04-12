<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;
    protected $table = "wh_tag";

    protected $fillable =  ["name", "color"];

    protected $hidden = ['pivot'];
}
