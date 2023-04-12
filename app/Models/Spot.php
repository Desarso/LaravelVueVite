<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Spot extends Model
{
    use SoftDeletes;
    protected $table = "wh_spot";
    protected $fillable =  ["name", "shortname", "alias", "idtype", "idparent", "idexternal", "isbranch", "cleanable", "floor", "enabled"];

    //Relationships
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'idspot', 'id');
    }
  
    public function cleaningPlans()
    {
        return $this->hasMany('App\Models\Cleaning\CleaningPlan', 'idspot', 'id');
    }

    public function currentCleaning()
    {
        return $this->hasOne('App\Models\Cleaning\CleaningPlan', 'id', 'idcleaningplan');
    }

    public function checklistOptions()
    {
        return $this->hasMany(ChecklistOption::class, 'idspot', 'id');
    }

    public function cleaningStatus()
    {
        return $this->hasOne('App\Models\Cleaning\CleaningStatus', 'id','idcleaningstatus');
    }

    public function parent()
    {
        return $this->hasOne(Spot::class,'id','idparent');
    }

    public function children()
    {
        return $this->hasMany(Spot::class, 'idparent', 'id');
    }

    public function spotType()
    {
        return $this->hasOne(SpotType::class, 'id','idtype');
    }
    
     // Accesors
     public function getIsbranchAttribute($value)
     {
         $value = $value == 1 ? $value = true : $value = false;
         return $value;
     }
     public function getEnabledAttribute($value)
     {
         $value = $value == 1 ? $value = true : $value = false;
         return $value;
     }
  
     public function getCleanableAttribute($value)
     {
         $value = $value == 1 ? $value = true : $value = false;
         return $value;
     }
  
     public function getShortnameAttribute($value)
     {
         $value = is_null($value) ? $this->attributes['name'] : $value;
         return $value;
     }
 
     // Mutators
     public function setIsbranchAttribute($value)
     {          
         $value == "true" ? $this->attributes['isbranch'] = 1 : $this->attributes['isbranch'] = 0;         
     }
     public function setEnabledAttribute($value)
     {         
         $value == "true" ? $this->attributes['enabled'] = 1 : $this->attributes['enabled'] = 0;   
     }
     public function setCleanableAttribute($value)
     {         
         $value == "true" ? $this->attributes['cleanable'] = 1 : $this->attributes['cleanable'] = 0;   
     }

     public function  getSpotWithChidrens($spots, $isbranch = false)
     {
         $data        = array();
         $data_spots  = array();
         $childs      = array();
 
         $models = DB::table('wh_spot')
                        ->when($isbranch == true, function ($query) {
                            return $query->where('isbranch', 1);
                        })
                        ->select('id', 'idparent')
                        ->get();
 
         for($i=0; $i < count($spots); $i++)
         {
             $this->getAllChildsSpot($spots[$i], $models, $data);
             $childs = array_merge($data_spots, $data);
         }
 
         return $childs;
     }

     public function getAllChildsSpot($id, $models, &$result = array())
     {
         $spot = $models->firstWhere('id', $id);

         if(is_null($spot)) return;
         
         array_push($result, $spot->id);
         
         $data = $models->where('idparent', $spot->id);

         foreach($data as $value)
         {
             $this->getAllChildsSpot($value->id, $models, $result);
         }
     }

     


}
