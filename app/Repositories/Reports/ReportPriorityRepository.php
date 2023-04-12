<?php

namespace App\Repositories\Reports;

use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Enums\TicketStatus;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Session;

class ReportPriorityRepository
{
    protected $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository;
    }

    public function getData($request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();
        $userSpots = $this->userRepository->getUserSpots(Auth::id());

        $data = Ticket::when(!is_null($request->idpriority), function ($query) use ($request) {
            return $query->where('idpriority', $request->idpriority);
        })
            ->when(!is_null($request->idstatus), function ($query) use ($request) {
                return $query->where('idstatus', $request->idstatus);
            })
            ->when(!is_null($request->idteam), function ($query) use ($request) {
                return $query->where('idteam', $request->idteam);
            })
            ->when(!is_null($request->iduser), function ($query) use ($request) {
                return $query->whereHas('users', function ($q) use ($request) {
                    $q->where('iduser', $request->iduser);
                });
            })
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('idspot', $userSpots)
            ->get(['id', 'code', 'name', 'idteam', 'idstatus', 'idspot', 'idpriority', 'duedate', 'finishdate', 'justification', 'created_at']);
        return $data;
    }

    public function getTicketPriority($request)
    {
        $tickets = $this->getData($request);

        foreach ($tickets as $ticket) {
            $ticket->delayed   = $this->isDelayed($ticket) ? 1 : 0;
            $ticket->postponed = $this->isPostponed($ticket) ? 1 : 0;
        }

        return $tickets;
    }

    public function getDataPriority($request)
    {
        $collection = collect([]);

        $data = $this->getData($request);

        $grouped = $data->groupBy('idpriority');

        foreach ($grouped as $idpriority => $tickets) {
            $item = $this->formatDataPriority($idpriority, $tickets);
            $collection->push($item);
        }

        return $collection;
    }

    private function formatDataPriority($idpriority, $tickets)
    {
        $delayed   = 0;
        $postponed = 0;

        foreach ($tickets as $ticket) {
            if ($this->isDelayed($ticket)) $delayed++;
            if ($this->isPostponed($ticket)) $postponed++;
        }

        return ['idpriority' => $idpriority, 'total' => $tickets->count(), 'delayed' => $delayed, 'postponed' => $postponed, 'percent' => $this->getAverageDelayed($tickets->count(), $delayed, $postponed)];
    }

    private function getDataUserPriority($request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();
        $userBranches = $this->userRepository->getUserBranch();

        $data = User::select('id')
            ->with(["tickets" => function ($query) use ($request, $start, $end) {
                $query->select('idticket', 'code', 'name', 'idteam', 'idstatus', 'idpriority', 'duedate', 'finishdate', 'justification', 'wh_ticket.created_at')
                    ->whereBetween('wh_ticket.created_at', [$start, $end])
                    ->when(!is_null($request->idpriority), function ($query) use ($request) {
                        return $query->where('idpriority', $request->idpriority);
                    })
                    ->when(!is_null($request->idstatus), function ($query) use ($request) {
                        return $query->where('idstatus', $request->idstatus);
                    })
                    ->when(!is_null($request->idteam), function ($query) use ($request) {
                        return $query->where('idteam', $request->idteam);
                    });
            }])
            ->when(!is_null($request->iduser), function ($query) use ($request) {
                return $query->whereHas('tickets', function ($q) use ($request) {
                    $q->where('iduser', $request->iduser);
                });
            })
            ->where(function ($query) use ($userBranches) {
                $query->whereJsonContains('spots', $userBranches[0]);

                foreach ($userBranches as $branch) {
                    $query->orWhereJsonContains('spots', $branch);
                }
            })
            ->get();

        return $data;
    }

    public function getUserPriority($request)
    {
        $users = $this->getDataUserPriority($request);

        foreach ($users as $user) {
            $delayed = 0;
            $postponed = 0;

            $user->total = $user->tickets->count();

            foreach ($user->tickets as $ticket) {
                if ($this->isDelayed($ticket)) $delayed++;
                if ($this->isPostponed($ticket)) $postponed++;
            }

            $user->delayed    = $delayed;
            $user->postponed  = $postponed;
            $user->efficiency = $this->getAverage($user->total, $delayed, $postponed);
            $user->pending    = $user->tickets->where('idstatus', TicketStatus::Pending)->count();
            $user->finished   = $user->tickets->where('idstatus', TicketStatus::Finished)->count();
        }

        return $users;
    }

    public function getEfficiencyPriority($request)
    {
        $tickets = $this->getData($request);

        $delayed   = 0;
        $postponed = 0;

        $total = $tickets->count();

        foreach ($tickets as $ticket) {
            if ($this->isDelayed($ticket)) $delayed++;
            if ($this->isPostponed($ticket)) $postponed++;
        }

        $average  = $this->getAverage($total, $delayed, $postponed);

        return ["total" => $total, "delayed" => $delayed, "average" => $average, "postponed" => $postponed];
    }

    private function isDelayed($ticket)
    {
        if (is_null($ticket->duedate)) return false;

        $limitDate = ($ticket->idstatus == TicketStatus::Finished) ? $ticket->finishdate : Carbon::now(Session::get('local_timezone'));

        return ($limitDate->greaterThan($ticket->duedate) ? true : false);
    }

    private function isPostponed($ticket)
    {
        return is_null($ticket->justification) ? false : true;
    }

    private function getAverage($total, $delayed, $postponed)
    {
        $delayed = ($delayed - $postponed);

        if ($delayed == 0) return 100;

        $ontime = ($total - $delayed);

        return $total == 0 ? 100 : round(($ontime / $total) * 100);
    }

    private function getAverageDelayed($total, $delayed, $postponed)
    {
        if ($delayed >= $postponed) {
            $delayed = ($delayed - $postponed);
        }

        if ($delayed == 0) return 0;

        $ontime = ($total - $delayed);

        return $total == 0 ? 0 : round(($delayed / $total) * 100);
    }
}
