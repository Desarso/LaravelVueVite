<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProtocolType extends Model
{
    
    protected $table = "wh_protocol_type";
    protected $fillable = ["name", "description"];

 
}
