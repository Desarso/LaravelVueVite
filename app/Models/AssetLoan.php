<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Session;

class AssetLoan extends Model
{
    use SoftDeletes;
    protected $table = "wh_asset_loan";
    protected $fillable =  ["idasset", "status", "iduser", "create_by", "duedate", "signature", "comment", "returned_date", "iduser_returned"];

    //Accesors
    public function getDuedateAttribute($value) 
    {     
        if(!is_null($value))
        {
            return Carbon::parse($value)->setTimezone('America/Costa_Rica');
        } 
    }

    public function getPurchaseDateAttribute($value) 
    {     
        if(!is_null($value))
        {
            return Carbon::parse($value)->setTimezone('America/Costa_Rica');
        } 
    }

    //Muttators
    public function setDuedateAttribute($value)
    {
        if(!is_null($value))
        {
            $this->attributes['duedate'] = Carbon::parse($value, 'America/Costa_Rica')->setTimezone(config('app.timezone'));
        }
    }

    public function setPurchaseDateAttribute($value)
    {
        if(!is_null($value))
        {
            $this->attributes['purchase_date'] = Carbon::parse($value, 'America/Costa_Rica')->setTimezone(config('app.timezone'));
        }
    }

    public function status()
    {
        return $this->hasOne(AssetStatus::class, 'id', 'idstatus');
    }

    public function asset()
    {
        return $this->hasOne(Asset::class, 'id', 'idasset');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'iduser');
    }

    public function createBy()
    {
        return $this->hasOne(User::class, 'id', 'create_by');
    }

    public function userReturned()
    {
        return $this->hasOne(User::class, 'id', 'iduser_returned');
    }

    public function notes()
    {
        return $this->hasMany(AssetLoanNote::class, 'idassetloan', 'id');
    }
}
