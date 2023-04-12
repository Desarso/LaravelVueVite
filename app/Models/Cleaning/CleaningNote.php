<?php

namespace App\Models\Cleaning;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CleaningNote extends Model
{
    protected $table = "wh_cleaning_note";

    protected $fillable = ["idplaner", "note", "type", "created_by", "created_at"];

    // Relations

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
