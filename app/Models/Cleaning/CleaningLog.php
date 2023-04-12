<?php

namespace App\Models\Cleaning;

use Illuminate\Database\Eloquent\Model;

class CleaningLog extends Model
{
    protected $table = "wh_cleaning_log";

    protected $fillable = ["action", "idspot", "iduser", "data"];
}
