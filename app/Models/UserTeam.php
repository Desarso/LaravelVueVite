<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserTeam extends Model
{
    protected $table = "wh_user_team";

    protected $fillable =  ["idteam", "iduser", "idrole", "core_team"];

    //Relationships
    public function user()
    {
        return $this->hasOne(User::class, 'id','iduser');
    }

    //Accesors
    public function getCoreTeamAttribute($value)
    {
        $value = $value == 1 ? $value = true : $value = false;
        return $value;
    }

    //Mutators
    public function setCoreTeamAttribute($value)
    {         
        $value == "true" ? $this->attributes['core_team'] = 1 : $this->attributes['core_team'] = 0;   
    }

    static public function getUserPermissions($iduser)
    {
        return DB::table('wh_user_team as ut')
                ->join('wh_role as r', 'r.id', '=', 'ut.idrole')
                ->where('ut.iduser', $iduser)
                ->select('ut.idteam', 'r.permissions')
                ->get();
    }
}
