<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSet extends Model
{
    protected $table = "wh_booking_set";
    protected $fillable = ["idimpala", "contact", "bookingIds", "startdate", "enddate"];

}
