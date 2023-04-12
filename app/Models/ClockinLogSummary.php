<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ClockinLogSummary extends Model
{
    protected $table = "wh_clockin_log_summary";
    protected $fillable =  ['iduser', 'regular_time', 'overtime', 'double_time', 'regular_time_approved', 'overtime_approved', 'double_time_approved', 'date', 'late_time', 'isholiday', 'status', 'note_approved', 'date_approved', 'idapprover'];


    public function approver()
    {
        return $this->hasOne(User::class,'id','idapprover');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id','iduser');
    }
}
