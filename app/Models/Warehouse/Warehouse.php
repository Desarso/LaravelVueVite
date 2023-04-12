<?php

namespace App\Models\Warehouse;

use App\Models\User;
use App\Models\TicketPriority;
use App\Models\Spot;
use App\Models\Warehouse\WarehouseItem;
use App\Models\Warehouse\WarehouseStatus;
use Chelout\RelationshipEvents\Concerns\HasOneEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasOneEvents;

    use SoftDeletes;

    protected $table = "wh_warehouse";

    protected $fillable = ['oc', 'idstatus', 'idspot', 'idpriority', 'iditem', 'idticket', 'iduser', 'amount', 'description', 'idsupplier', 'coin', 'cost'];

    //Relationships
    public function status()
    {
        return $this->hasOne(WarehouseStatus::class, 'id', 'idstatus');
    }

    public function priority()
    {
        return $this->hasOne(TicketPriority::class, 'id','idpriority');
    }
    
    public function spot()
    {
        return $this->hasOne(Spot::class,'id','idspot');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'iduser');
    }

    public function item()
    {
        return $this->hasOne(WarehouseItem::class, 'id', 'iditem');
    }

    public function logs()
    {
        return $this->hasMany(WarehouseLog::class, 'idwarehouse','id');
    }

    public function notes()
    {
        return $this->hasMany(WarehouseNote::class, 'idwarehouse', 'id');
    }

    //Muttators
    public function setCostAttribute($value)
    {
        $this->attributes['cost'] = (is_null($value) ? 0 : $value);
    }
}

