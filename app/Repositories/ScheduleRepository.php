<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\BookingStatus;
use App\Models\Schedule;
use App\Models\ScheduleDetail;
use App\Models\User;
use App\Models\Team;
use App\Models\UserSchedule;
use Carbon\Carbon;

class ScheduleRepository
{
    protected $defaultIdSchedule;

    public function __construct()
    {
        //$this->defaultIdSchedule = DB::table('wh_schedule')->where('idtype', 4)->first()->id;
    }

    public function getAll()
    {
        $data = DB::table('wh_schedule')->get();

        $data->map(function ($item){
            $item->teams = (is_null($item->teams) ? [] : array_map(array($this, 'formatTeams'), json_decode($item->teams)));
            return $item;
        });

        return $data;
    }

    public function getList()
    {
        return Schedule::get(['id as value', 'name as text']);
    }

    public function getListScheduleType()
    {
        return DB::table('wh_schedule_type')->get(['id as value', 'name as text']);
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
                            $q->where('idschedule', $request->idschedule);
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
            return $schedules[$i]->idschedule;
        }

        return null;
    }

    public function create($request)
    {
        $teams = $this->pluckTeams($request->teams);
        $request->merge(['teams' => json_encode($teams)]);

        $model = Schedule::create($request->all());
        $model->teams = array_map(array($this, 'formatTeams'), $teams);
        return $model;
    }

    public function update($request)
    {
        $teams = $this->pluckTeams($request->teams);
        $request->merge(['teams' => json_encode($teams)]);

        $model = Schedule::find($request->id);
        $model->fill($request->all())->save();
        
        $model->teams = array_map(array($this, 'formatTeams'), $teams);

        return $model;
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
                        ['idschedule' => $value]
                    );

                    $date->addDays(1);
                }
            }
        }

        return $users;
    }

    public function delete($request)
    {
        $model = Schedule::findOrFail($request->id);
        $model->delete();
    }

    public function getScheduleDetails($request)
    {
        $data = null;

        $detail = ScheduleDetail::where('idschedule', $request->idschedule)->first();

        if(is_null($detail))
        {
            $data = $this->getDefaultDetails();
        }
        else
        {
            $data = ScheduleDetail::where('idschedule', $request->idschedule)->get();
        }

        return $data;
    }

    public function updateScheduleDetails($request)
    {
        $data = json_decode($request->days);

        foreach ($data as $item)
        {
            //if(is_null($item->start_time) || is_null($item->end_time)) continue;

            $scheduleDetail = ScheduleDetail::updateOrCreate(
                ['idschedule' => $request->idschedule, 'day' => $item->day],
                ['start_time' => $item->start_time, 'end_time' => $item->end_time]
            );
        }
    }

    public function getDefaultDetails()
    {
        $days = collect([
            ['day' => 'MON',   'start_time' => null, 'end_time' => null],
            ['day' => 'TUES',  'start_time' => null, 'end_time' => null],
            ['day' => 'WED',   'start_time' => null, 'end_time' => null],
            ['day' => 'THURS', 'start_time' => null, 'end_time' => null],
            ['day' => 'FRI',   'start_time' => null, 'end_time' => null],
            ['day' => 'SAT',   'start_time' => null, 'end_time' => null],
            ['day' => 'SUN',   'start_time' => null, 'end_time' => null]
        ]);

        return $days;
    }

    private function formatTeams($idteam)
    {
        $team = new \stdClass;
        $team->value = $idteam;
        return $team;
    }

    private function pluckTeams($teams)
    {
        $teams = collect($teams)->pluck('value')->toArray();
        $teams = array_map('intval', $teams);
        return $teams;
    }
}