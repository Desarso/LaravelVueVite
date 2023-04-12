<?php

namespace App\Models\Cleaning;

use Illuminate\Database\Eloquent\Model;

class CleaningChecklist extends Model
{
    protected $table = "wh_cleaning_checklist";

    protected $fillable = ["idplaner","idchecklist", "options", "results", "created_at"];

}
