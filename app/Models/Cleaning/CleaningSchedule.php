<?php

namespace App\Models\Cleaning;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Session;

class CleaningSchedule extends Model
{
	use SoftDeletes;
	protected $table = "wh_cleaning_schedule";
	protected $fillable =  ["idspot","iduser","dow","time", "sequence", "iditem", "enabled"];

	//Accesors
	public function getEnabledAttribute($value)
	{
		$value = $value == 1 ? $value = true : $value = false;
	  	return $value;
	}

	public function getTimeAttribute($value) 
	{     
		if(!is_null($value))
		{
			return Carbon::parse($value);
		} 
	}
   
	//Muttators
	public function setTimeAttribute($value)
	{
		$this->attributes['time'] = (is_null($value) ? null : Carbon::parse($value, Session::get('local_timezone'))->setTimezone(config('app.timezone')));
	}

	public function setEnabledAttribute($value)
	{         
		$value == "true" ? $this->attributes['enabled'] = 1 : $this->attributes['enabled'] = 0;   
	}
}
