<?php

namespace App\Models\Production;

use App\Models\Production\ProductionStatus;
use App\Models\Production\ProductDestination;
use App\Models\Production\Product;
use App\Models\Production\Presentation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Session;
use Carbon\Carbon;

class Production extends Model
{
    use SoftDeletes;
    protected $table = "wh_production";
    //TODO: agregar "productiondate" ...da error formato fecha
    protected $fillable =  ["idequipment","idschedule", "idproduct", "idpresentation", "iddestination", "idstatus", "productiongoal", "productionorder", "lot", "idoperator", "productiondate"];
    

 

    //Accesors
    public function getProductiondateAttribute($value) 
    {     
        if(!is_null($value))
        {
            return Carbon::parse($value)->setTimezone(Session::get('local_timezone'));
        } 
    }


    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'idproduct');
    }
    
    public function operator()
    {
        return $this->hasOne(User::class, 'id', 'idoperator');
    }

    public function status()
    {
        return $this->hasOne(ProductionStatus::class, 'id', 'idstatus');
    }

    public function presentation()
    {
        return $this->hasOne(Presentation::class, 'id', 'idpresentation');
    }

    public function destination()
    {
        return $this->hasOne(ProductDestination::class, 'id', 'iddestination');
    }

    public function schedule()
    {
        return $this->hasOne(ProductionSchedule::class, 'id', 'idschedule');
    }

    public function logs()
    {
        return $this->hasMany(ProductionLog::class, 'idproduction', 'id');
    }

}
