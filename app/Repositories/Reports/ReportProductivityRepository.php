<?php

namespace App\Repositories\Reports;
use Illuminate\Support\Facades\DB;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Session;

class ReportProductivityRepository
{
    protected $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository;
    }

    public function getProductivityByTeam($request)
    {     
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();
        $userSpots = $this->userRepository->getUserSpots(Auth::id());

        $teams =  DB::table('wh_ticket AS t')
                    ->select(
                        't.idteam', 
                        DB::raw('COUNT(distinct t.id) as total'),
                        DB::raw('COUNT(distinct  users.iduser) as num_users'),
                        DB::raw('COUNT(DISTINCT IF(t.idstatus = 4, t.id, null)) as finished'),
                        DB::raw('SUM(DISTINCT duration) as duration')
                    )
                    ->leftJoin('wh_ticket_user AS users', 'users.idticket', '=', 't.id')
                    ->when(isset($request->idteam), function ($query) use ($request) {
                        return $query->where('t.idteam', $request->idteam);
                    })
                    ->when(isset($request->iduser), function ($query) use ($request) {
                        return $query->where('users.iduser', $request->iduser);
                    })
                    ->whereIn('idspot', $userSpots)
                    ->whereBetween('t.created_at', [$start, $end])
                    ->whereNull('t.deleted_at')
                    ->orderBy('t.idteam')
                    ->groupBy('t.idteam')
                    ->get();

        foreach ($teams as $team)
        {
            $efectivity = ($team->finished / $team->total) * 100;
            $team->efectivity = round($efectivity);

            $productivity = $this->calculateProductivity($request, $team);
            $team->productivity = round($productivity);
        }

        return $teams;
        
    }

    public function getProductivityByUser($request)
    {     
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();
        $userBranches = $this->userRepository->getUserBranch();

        $users =  DB::table('wh_user AS u')
                    ->select(
                        'u.id', 
                        DB::raw('CONCAT(`u`.firstname, " ", `u`.lastname) as name'),
                        'u.urlpicture', 
                        DB::raw('COUNT(distinct users.idticket) as total'),
                        DB::raw('SUM( IF(t.idstatus = 4, 1, 0)) as finished'),
                        DB::raw('SUM(duration) as duration')
                    )
                    ->leftJoin('wh_ticket_user AS users', 'users.iduser', '=', 'u.id')
                    ->join('wh_ticket AS t', 't.id', '=', 'users.idticket')
                    ->when(isset($request->idteam), function ($query) use ($request) {
                        return $query->where('t.idteam', $request->idteam);
                    })
                    ->when(isset($request->iduser), function ($query) use ($request) {
                        return $query->where('u.id', $request->iduser);
                    })
                    ->where(function ($query) use ($userBranches) {
                        $query->whereJsonContains('spots', $userBranches[0]);
        
                        foreach ($userBranches as $branch) {
                            $query->orWhereJsonContains('spots', $branch);
                        }
                    })
                    ->whereBetween('t.created_at', [$start, $end])
                    ->whereNull('t.deleted_at')
                    ->orderBy('u.firstname')
                    ->groupBy('u.id')
                    ->get();

        foreach ($users as $user)
        {
            $efectivity = ($user->finished / $user->total) * 100;
            $user->efectivity = round($efectivity);

            $user->num_users = 1;
            $productivity = $this->calculateProductivity($request, $user);
            $user->productivity = round($productivity);
        }

        return $users;
    }

    public function getProductivityGeneral($request)
    {     
        $userSpots = $this->userRepository->getUserSpots(Auth::id());
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        $data =  DB::table('wh_ticket AS t')
                    ->select(
                        DB::raw('COUNT(DISTINCT t.id) as total'),
                        DB::raw('COUNT(DISTINCT IF(t.idstatus = 4, t.id, null)) as finished'),
                        DB::raw('SUM(DISTINCT duration) as duration'),
                        DB::raw('COUNT(DISTINCT  users.iduser) as num_users'),
                        DB::raw('COUNT(DISTINCT IF(t.approved = 1, t.id, null)) as approved'),
                        DB::raw('COUNT(DISTINCT IF(t.approved = 0, t.id, null)) as reprobate'),
                        DB::raw('1 as reopen')
                    )
                    ->leftJoin('wh_ticket_user AS users', 'users.idticket', '=', 't.id')
                    ->when(isset($request->idteam), function ($query) use ($request) {
                        return $query->where('t.idteam', $request->idteam);
                    })
                    ->when(isset($request->iduser), function ($query) use ($request) {
                        return $query->where('users.iduser', $request->iduser);
                    })
                    ->whereIn('idspot', $userSpots)
                    ->whereBetween('t.created_at', [$start, $end])
                    ->whereNull('t.deleted_at')
                    ->first();

        $efectivity = 0;
        if ($data->total != 0) {
            $efectivity = ($data->finished / $data->total) * 100;
        }
        $data->efectivity = round($efectivity);

        $productivity = $this->calculateProductivity($request, $data);
        $data->productivity = round($productivity);

        return response()->json($data);
    }

    private function calculateProductivity($request, $team)
    {  
        $start = Carbon::createFromDate($request->start);
        $end = Carbon::createFromDate($request->end);
        $diffDays = $start->diffInDays($end);

        $hours = floor($team->duration / 3600);
        $hours_by_person = $team->num_users * 7; 

        if ($hours_by_person == 0 || $hours == 0 ) {
            return 0;
        }

        if ($diffDays == 0) $diffDays = 1;

        $productivity = ($hours / ($diffDays * $hours_by_person)) * 100; 

        return round($productivity);
    }
}