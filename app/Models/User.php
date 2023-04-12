<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;

class User extends Model
{
    use HasBelongsToManyEvents;
    use SoftDeletes;

    protected $observables = [
        'belongsToManyAttached',
        'belongsToManyDetached'
    ];
    protected $table = "wh_user";

    protected $fillable = ['firstname', 'lastname', 'nickname', 'username', 'password', 'email', 'phonenumber', 'job', "enabled", "idstatus", "isadmin", "urlpicture", "birthdate", "gender", "online", "idlevel", "credits", "version", "idschedule", "forcelogin", "chat_uid", "clockin_code"];

    protected $hidden = ['pivot'];

    protected $appends = ["fullname"];
    
    //Relationships
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'wh_user_team', 'iduser', 'idteam');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'wh_user_team', 'iduser', 'idrole')->withPivot('idteam')->orderBy('idteam');;
    }

    public function coreTeam()
    {
        return $this->belongsToMany(Team::class, 'wh_user_team', 'iduser', 'idteam')->where('core_team', 1);
    }

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'wh_ticket_user', 'iduser', 'idticket');
    }    

    public function reports()
    {
        return $this->hasMany(Ticket::class, 'created_by', 'id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    public function attendances()
    {
        return $this->hasMany(UserAttendance::class, 'iduser', 'id');
    }

    public function latestAttendance()
    {
        return $this->hasOne(UserAttendance::class, 'iduser', 'id')->latest();
    }

    public function cleaningPlans()
    {
        return $this->hasMany('App\Models\Cleaning\CleaningPlan', 'iduser', 'id');
    }

    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'wh_notification_user', 'iduser', 'idnotification')->latest();
    }

    public function schedules()
    {
        return $this->hasMany(UserSchedule::class, 'iduser', 'id');
    }

    public function clockin()
    {
        return $this->hasOne(ClockinLog::class, 'iduser', 'id')->latest();
    }

     // Accesors
     public function getAttributeIsAdmin($value)
     {
         $value = $value == 1 ? $value = true : $value = false;
         return $value;
     }

     public function getEnabledAttribute($value)
     {
         $value = $value == 1 ? $value = true : $value = false;
         return $value;
     }

     public function getOnlineAttribute($value)
     {
         $value = $value == 1 ? $value = true : $value = false;
         return $value;
     }
 
     // Mutators
     public function setIsAdminAttribute($value)
     {          
         $value == "true" ? $this->attributes['isadmin'] = 1 : $this->attributes['isadmin'] = 0;         
     }
     
     public function setEnabledAttribute($value)
     {         
         $value == "true" ? $this->attributes['enabled'] = 1 : $this->attributes['enabled'] = 0;   
     }

     public function setOnlineAttribute($value)
     {         
         $value == "true" ? $this->attributes['online'] = 1 : $this->attributes['online'] = 0;   
     }
}
