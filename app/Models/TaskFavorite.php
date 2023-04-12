<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskFavorite extends Model
{
    protected $table = "wh_tasks_favorite";

    protected $fillable =  ["name", "iduser", "iditem", "idspot"];

    public function spot()
    {
        return $this->hasOne(Spot::class,'id','idspot')->withTrashed();
    }

    public function item()
    {
        return $this->hasOne(Item::class, 'id','iditem')->withTrashed('item.deleted_at');
    }
}
