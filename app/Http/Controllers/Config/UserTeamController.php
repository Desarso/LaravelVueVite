<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserTeamRepository;

class UserTeamController extends Controller
{
    protected $userTeam;

    public function __construct()
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->userTeam = new UserTeamRepository;
    }

    public function getUserTeams(Request $request)
    {
        return $this->userTeam->getUserTeams($request);
    }

    public function create(Request $request)
    {
        return $this->userTeam->create($request);
    }

    public function update(Request $request)
    {
        return $this->userTeam->update($request);
    }

    public function delete(Request $request)
    {
        return $this->userTeam->delete($request);
    }
}
