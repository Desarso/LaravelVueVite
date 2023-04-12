<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\UserTeam;

class UserTeamRepository
{
    public function getUserTeams($request)
    {
        return UserTeam::where('idteam', $request->idteam)
                       ->join('wh_user', 'wh_user_team.iduser', '=', 'wh_user.id')
                       ->select('wh_user_team.id', 'iduser', 'idteam', 'idrole', 'core_team')
                       ->orderBy('wh_user.firstname', 'ASC')
                       ->get();
    }

    public function create($request)
    {
        return $this->updateOrCreate($request);
    }

    public function update($request)
    {
        return $this->updateOrCreate($request);
    }

    public function delete($request)
    {
        $userTeam = UserTeam::find($request->id);        
        $userTeam->delete();
        return $userTeam;
    }

    private function updateOrCreate($request)
    {
        $userTeam = UserTeam::updateOrCreate(
            ["idteam" => $request->idteam, "iduser" => $request->iduser],
            ["idrole" => $request->idrole, "core_team" => $request->core_team]
        );

        return $userTeam;
    }

    public function getUserPermissionsAPP($iduser)
    {
        return UserTeam::getUserPermissions($iduser);        
    }
}