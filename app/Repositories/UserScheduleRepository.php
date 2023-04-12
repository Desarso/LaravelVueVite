<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSchedule;
use App\Models\User;
use App\Models\Shift;
use Carbon\Carbon;

class UserScheduleRepository
{
    protected $defaultIdSchedule;

    public function __construct()
    {
        $this->defaultIdSchedule = DB::table('wh_shift')->where('id', 9999)->first()->id;
    }

    public function getUserSchedule($request)
    {
        $start = Carbon::createFromFormat('!Y-m-d', $request->start); 
        $end = Carbon::createFromFormat('!Y-m-d', $request->end); 

        $userTeams = User::where('id', Auth::id())
                         ->with(["teams" => function ($q) {
                            $q->whereJsonContains('bosses', Auth::id());
                        }])
                         ->first()
                         ->teams
                         ->pluck('id')
                         ->toArray();

        $settings = json_decode(DB::table('wh_organization')->first()->settings);
        $rhTeam = null;

        if(property_exists($settings, 'rh_team')) {
            $rhTeam = $settings->rh_team;
        }

        $data = User::when(!is_null($request->iduser), function ($query) use ($request) {
                        return $query->where('id', $request->iduser);
                   })
                   ->when(!is_null($request->idschedule), function ($query) use ($request) {
                        return $query->whereHas('schedules', function ($q) use ($request) {
                            $q->where('idshift', $request->idschedule);
                        });
                    })
                   ->when(!is_null($request->idteam), function ($query) use ($request) {
                        return $query->whereHas('teams', function ($q) use ($request) {
                            $q->where('core_team', 1)->where('idteam', $request->idteam);
                        });
                   })
                   ->with(["schedules" => function ($q) use ($start, $end) {
                       $q->whereBetween('date', [$start, $end]);
                    }])
                    ->with(["teams" => function ($q) {
                        $q->where('core_team', 1);
                    }])
                    ->when(!in_array($rhTeam, $userTeams), function ($query) use ($userTeams) {
                         return $query->whereHas('teams', function ($q) use ($userTeams) {
                            $q->where('core_team', 1)->whereIn('idteam', $userTeams);
                        });
                    })
                    ->where('enabled', 1)
                   ->orderBy('firstname')
                   ->get(['id']);
        

        $collection = collect();

        foreach($data as $user)
        {
            $userSchedule = (object) array(
                "id"    => $user->id,
                "SUN"   => $this->getSchedule($user->schedules, 0),
                "MON"   => $this->getSchedule($user->schedules, 1),
                "TUES"  => $this->getSchedule($user->schedules, 2),
                "WED"   => $this->getSchedule($user->schedules, 3),
                "THURS" => $this->getSchedule($user->schedules, 4),
                "FRI"   => $this->getSchedule($user->schedules, 5),
                "SAT"   => $this->getSchedule($user->schedules, 6)
            );

            $collection->push($userSchedule);
        }

        return $collection;
    }  

    private function getSchedule($schedules, $i)
    {
        if($schedules->count() > 0)
        {
            return $schedules[$i]->idshift;
        }

        return null;
    }

    public function updateUserSchedule($request)
    {
        $users = array();

        $rows = json_decode($request->models);

        foreach($rows as $key => $row)
        {
            $date = Carbon::createFromFormat('!Y-m-d', $request->start);

            foreach ($row as $key => $value)
            {
                if($key != "id")
                {
                    if(is_null($value))
                    {
                        $value = $this->defaultIdSchedule;
                    }

                    
                    $userSchedule = UserSchedule::updateOrCreate(
                        ['iduser'     => $row->id, 'date' => $date],
                        ['idshift' => $value]
                    );

                    $date->addDays(1);
                }
            }
        }

        return $users;
    }

    public function getListShift()
    {
        return Shift::get(['id as value', 'name as text']);
    }

}