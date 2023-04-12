<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;
use Illuminate\Support\Facades\Auth;
use Session;

class Ticket extends Model
{
    use HasBelongsToManyEvents;
    use SoftDeletes;

 

    protected $observables = [
        'belongsToManyAttaching',
        'belongsToManyAttached',
        'belongsToManyDetaching',
        'belongsToManyDetached'
    ];
   
    protected $table = "wh_ticket";

    protected $fillable = ["uuid", "name", "idstatus", "startdate", "finishdate", "resumedate", "duration", "idspot", "idteam", "iditem", "description", "idpriority", "created_by", "byclient", "files", "approved", "created_at", "code", "quantity", "idasset", "updated_by", "duedate", "justification", "signature", "idplanner", "start", "end"];

    protected $hidden = ['pivot'];

    //Relationships
    public function users()
    {
        return $this->belongsToMany(User::class, 'wh_ticket_user', 'idticket', 'iduser')->where('copy', 0)->using(TicketUser::class);
    }

    public function usersCopy()
    {
        return $this->belongsToMany(User::class, 'wh_ticket_user', 'idticket', 'iduser')->where('copy', 1)->using(TicketUser::class);
    }

    public function usersAll()
    {
        return $this->belongsToMany(User::class, 'wh_ticket_user', 'idticket', 'iduser')
                    ->withPivot(["copy"]);
    }

    public function tempusers(){
        return $this->users();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'wh_ticket_tag', 'idticket', 'idtag');
    }

    public function approvers()
    {
        return $this->belongsToMany(User::class, 'wh_ticket_approver', 'idticket', 'iduser')->using(TicketUser::class);
    }

    public function createdby()
    {
        return $this->hasOne(User::class,'id','created_by')->withTrashed();
    }

    public function status()
    {
        return $this->hasOne(TicketStatus::class,'id','idstatus');
    }

    public function team()
    {
        return $this->hasOne(Team::class,'id','idteam');
    }

    public function updatedby()
    {
        return $this->hasOne(User::class,'id','updated_by');
    }
    
    public function spot()
    {
        return $this->hasOne(Spot::class,'id','idspot')->withTrashed();
    }
    
    public function notes()
    {
        return $this->hasMany(TicketNote::class, 'idticket', 'id');
    }

    public function checklists()
    {
        return $this->hasMany(TicketChecklist::class, 'idticket','id');
    }

    public function item()
    {
        return $this->hasOne(Item::class, 'id','iditem')->withTrashed('item.deleted_at');
    }

    public function priority()
    {
        return $this->hasOne(TicketPriority::class,'id','idpriority');
    }

    public function logs()
    {
        return $this->hasMany(Log::class, 'idticket','id');
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class, 'idticket', 'id');
    }

    public function planner()
    {
        return $this->hasOne(Planner::class, 'id','idplanner');
    }

    //Accesors
    public function getDuedateAttribute($value) 
    {     
        if(!is_null($value))
        {
            return Carbon::parse($value)->setTimezone(Session::get('local_timezone'));
        } 
    }

    public function getStartdateAttribute($value) 
    {    
        if(!is_null($value))
        {
            return Carbon::parse($value)->setTimezone(Session::get('local_timezone'));
        } 
    }

    public function getEndAttribute($value) 
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

    //Muttators
    public function setDuedateAttribute($value)
    {
        if(!is_null($value))
        {
            $this->attributes['duedate'] = Carbon::parse($value, Session::get('local_timezone'))->setTimezone(config('app.timezone'));
        }
        else
        {
            $this->attributes['duedate'] = null;
        }
    }

    public function setStartAttribute($value)
    {
        if(!is_null($value))
        {
            $this->attributes['start'] = Carbon::parse($value, Session::get('local_timezone'))->setTimezone(config('app.timezone'));
        }
    }

    public function setEndAttribute($value)
    {
        if(!is_null($value))
        {
            $this->attributes['end'] = Carbon::parse($value, Session::get('local_timezone'))->setTimezone(config('app.timezone'));
        }
    }

    public function setApprovedAttribute($value)
    {
        if(!is_null($value))
        {
            $this->attributes['approved'] = $value;
        }
    }
}
