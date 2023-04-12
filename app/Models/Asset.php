<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Session;

class Asset extends Model
{
    use SoftDeletes;
    
    protected $table = "wh_asset";

    protected $fillable =  ["name", "idcategory", "idstatus", "code", "photo", "model", "purchase_date", "cost", "description", "isloaned"];

    public function category()
    {
        return $this->hasOne(AssetCategory::class, 'id', 'idcategory');
    }

    public function status()
    {
        return $this->hasOne(AssetStatus::class, 'id', 'idstatus');
    }

    public function loans()
    {
        return $this->hasMany(AssetLoan::class, 'idasset', 'id');
    }

    //Accesors
    public function getPurchaseDateAttribute($value) 
    {     
        if(!is_null($value))
        {
            return Carbon::parse($value)->setTimezone(Session::get('local_timezone'));
        } 
    }

    public function getIsloanedAttribute($value)
    {
        $value = $value == 1 ? $value = true : $value = false;
        return $value;
    }

    //Muttators
    public function setPurchaseDateAttribute($value)
    {
        if(!is_null($value))
        {
            $this->attributes['purchase_date'] = Carbon::parse($value, Session::get('local_timezone'))->setTimezone(config('app.timezone'));
        }
        else
        {
            $this->attributes['purchase_date'] = null;
        }
    }
    
    public function setIsloanedAttribute($value)
    {          
        $value == "true" ? $this->attributes['isloaned'] = 1 : $this->attributes['isloaned'] = 0;         
    }
}
