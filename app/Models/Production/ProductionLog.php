<?php

namespace App\Models\Production;
use Illuminate\Database\Eloquent\Model;

class ProductionLog extends Model
{
    protected $table = "wh_production_log";
    protected $fillable =  ["name", "type","idstop","idteam", "idstatus", "created_by","iduser","idproduction","idequipment", "started","finished","resumed","duration"];

     

     public function production()
    {
        return $this->hasOne(Production::class, 'id', 'idproduction');
    }
 
     

}
