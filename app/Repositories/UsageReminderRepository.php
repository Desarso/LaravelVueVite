<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserTeam;
use App\Models\Ticket;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class UsageReminderRepository
{
    private $config;

    public function __construct()
    {
        $this->config = new ConfigRepository;
    }

    public function getUserAvailable()
    {
        $users = User::with('teams:idteam,name,color')->with('roles:idrole,name')->withCount('tickets')->get();

        $users->each(function ($user, $key){
            $user->full_teams = join(", ", $user->teams->pluck('name')->all());
        });

        return $users;
    }

}