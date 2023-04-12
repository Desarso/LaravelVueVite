<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Session;

class Shift extends Model
{
    protected $table = "wh_shift";
    protected $fillable =  ['idschedule', 'idovertime', 'name', 'start', 'end', 'dow'];

    //Accesors
    public function getStartAttribute($value) 
	{     
		if(!is_null($value))
		{
			return Carbon::parse($value, 'America/Costa_Rica');
		} 
	}

    public function getEndAttribute($value) 
	{     
		if(!is_null($value))
		{
			return Carbon::parse($value, 'America/Costa_Rica');
		} 
	}

    //Muttators
	public function setStartAttribute($value)
	{
		$this->attributes['start'] = (is_null($value) ? null : Carbon::parse($value));
	}

    public function setEndAttribute($value)
	{
		$this->attributes['end'] = (is_null($value) ? null : Carbon::parse($value));
	}
}
