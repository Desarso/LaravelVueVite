<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketQr extends Model
{
    protected $table = "wh_qr";
    protected $fillable =  ["iditem", "idspot", "qr"];
}
