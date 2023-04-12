<?php

namespace App\Models\Cleaning;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Spot;
use App\Models\Item;
use App\Models\Cleaning\CleaningChecklist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Session;

class CleaningPlan extends Model
{
    use SoftDeletes;
    
    protected $table = "wh_cleaning_plan";
    protected $fillable =  ["date", "idcleaningstatus", "idspot", "iduser", "iditem", "cleanat", "startdate", "resumedate", "finishdate", "duration", "created_by"];

    // Relations
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'iduser');
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function item()
    {
        return $this->hasOne(Item::class, 'id', 'iditem');
    }

    public function spot()
    {
        return $this->hasOne(Spot::class, 'id', 'idspot');
    }

    public function checklists()
    {
        return $this->hasMany(CleaningChecklist::class, 'idplaner','id');
    }

    public function cleaningticket()
    {
        return $this->hasOne(CleaningChecklist::class, 'id', 'idticket');
    }

    public function cleaningStatus()
    {
        return $this->hasOne('App\Models\Cleaning\CleaningStatus', 'id','idcleaningstatus');
    }

    //Accesors
    public function getCleanatAttribute($value) 
    {     
        if(!is_null($value))
        {
            return Carbon::parse($value)->format('h:i A');
        } 
    }

    public function getStartdateAttribute($value) 
    {    
        if(!is_null($value))
        {
            return Carbon::parse($value)->setTimezone(Session::get('local_timezone'));
        } 
    }

    public function getFinishdateAttribute($value) 
    {     
        if(!is_null($value))
        {
            return Carbon::parse($value)->setTimezone(Session::get('local_timezone'));
        } 
    }

    public function getIduserAttribute($value) 
    {    
        return is_null($value) ? 0 : $value;
    }

    //Muttators
    public function setCleanatAttribute($value)
    {
        if(!is_null($value))
        {
            $this->attributes['cleanat'] = Carbon::parse($value);
        }
    }

    public function setIduserAttribute($value)
    {         
        $this->attributes['iduser'] = ($value == "0" ? null : $value);   
    }
}
