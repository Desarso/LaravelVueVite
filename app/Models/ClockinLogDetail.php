<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClockinLogDetail extends Model
{
    protected $table = "wh_clockin_log_detail";
    protected $fillable =  ['idclockin', 'start', 'end', 'iduser', 'rate', 'time'];

}
