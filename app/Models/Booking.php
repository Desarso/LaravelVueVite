<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
     protected $table = "wh_booking";
     protected $fillable =  ["idimpala","status","idbookingset","idspot","idtype","startdate","enddate","adultcount","childcount","infantcount"];
}
