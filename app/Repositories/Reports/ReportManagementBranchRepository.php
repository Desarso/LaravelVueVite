<?php

namespace App\Repositories\Reports;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TicketChecklist;
use App\Models\Spot;
use App\Models\ChecklistGroupWeight;
use Carbon\Carbon;
use App\Helpers\Helper;
use Session;
use App\Repositories\SpotRepository;

class ReportManagementBranchRepository
{
    protected $spotRepository;
    protected $spot;

    public function __construct()
    {
        $this->spotRepository = new SpotRepository;
        $this->spot =  new Spot;
    }

    public function getData($request)
    {
        $groups = ChecklistGroupWeight::with('subgroups')->whereNull('idparent')->where('idchecklist', $request->idchecklist)->get();

        $spots = $this->spot->getSpotWithChidrens((array) $request->idspot, true);

        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();
    
        $data = TicketChecklist::select('id', 'idticket', 'idchecklist', 'options', 'created_at')
                               ->with('ticket:id,idstatus,idspot,created_by,created_at')
                               ->has('ticket')
                               ->whereBetween('created_at', [$start, $end])
                               ->where('idchecklist', $request->idchecklist)
                               ->when(isset($request->idspot), function ($query) use ($spots) {
                                    $query->whereHas('ticket', function ($q) use ($spots) {
                                        $q->whereIn('idspot', $spots);
                                    });
                                })
                               ->latest()
                               ->get();

        $result = collect();

        foreach($data as $checklist)
        {
            $options = collect(json_decode($checklist->options));

            $headers = $options->where('optiontype', 6);

            $options = $options->where('optiontype', '!=', 6);

            $resultGroups = collect();

            $dataGroups = $options->groupBy('group');

            foreach ($groups as $group)
            {
                if($group->subgroups->count() > 0)
                {
                    $finalPercentage = 0;

                    foreach ($group->subgroups as $subgroup)
                    {
                        $subgroupCompliance = $this->getCompliance($dataGroups[$subgroup->group]);

                        $percentage = (($subgroupCompliance / 100) * $subgroup->weight);

                        $finalPercentage += $percentage;
                    }

                    $percentage = (($finalPercentage / 100) * $group->weight);

                    $item = ["group" => $headers->firstWhere('group', $group->group)->name, "compliance" => $finalPercentage, "percentage" => $percentage];

                    $resultGroups->push($item);

                    continue;
                }

                $compliance = $this->getCompliance($dataGroups[$group->group]);

                $percentage = (($compliance / 100) * $group->weight);

                $item = ["group" => $headers->firstWhere('group', $group->group)->name, "compliance" => round($compliance, 2), "percentage" => round($percentage, 2)];

                $resultGroups->push($item);
            }

            $totalCompliance = ($resultGroups->sum('compliance') / $groups->count());

            $totalPercentage = $resultGroups->sum('percentage');

            $item = ["idticket" => $checklist->idticket, "idspot" => $checklist->ticket->idspot, "percentage" => round($totalPercentage, 2), "compliance" => round($totalCompliance, 2), "iduser" => $checklist->ticket->created_by, "created_at" => $checklist->created_at];

            $result->push($item);
        }

        return $result;
    }

    public function getDataGroup($request)
    {
        $groups = ChecklistGroupWeight::with('subgroups')->whereNull('idparent')->where('idchecklist', $request->idchecklist)->get();

        $options = $this->getAllData($request);

        if($options->count() == 0) return [];

        $headers = $options->where('optiontype', 6);

        $options = $options->where('optiontype', '!=', 6);

        $resultGroups = collect();

        $dataGroups = $options->groupBy('group');

        foreach ($groups as $group)
        {
            if($group->subgroups->count() > 0)
            {
                $finalPercentage = 0;

                foreach ($group->subgroups as $subgroup)
                {
                    $subgroupCompliance = $this->getCompliance($dataGroups[$subgroup->group]);

                    $percentage = (($subgroupCompliance / 100) * $subgroup->weight);

                    $finalPercentage += $percentage;
                }

                $percentage = (($finalPercentage / 100) * $group->weight);

                $item = ["idgroup" => $group->group, "weight" => $group->weight, "group" => $headers->firstWhere('group', $group->group)->name, "compliance" => round($finalPercentage, 2), "percentage" => round($percentage, 2)];

                $resultGroups->push($item);

                continue;
            }

            $compliance = $this->getCompliance($dataGroups[$group->group]);

            $percentage = (($compliance / 100) * $group->weight);

            $item = ["idgroup" => $group->group, "weight" => $group->weight, "group" => $headers->firstWhere('group', $group->group)->name, "compliance" => round($compliance, 2), "percentage" => round($percentage, 2)];

            $resultGroups->push($item);
        }

        return $resultGroups;
    }

    public function getDataOption($request)
    {
        $groups = ChecklistGroupWeight::with('subgroups')->whereNull('idparent')->where('idchecklist', $request->idchecklist)->get();

        $options = $this->getAllData($request);

        $headers = $options->where('optiontype', 6);

        $options = $options->where('optiontype', '!=', 6);

        $resultGroups = collect();

        if($request->has('groups'))
        {
            $children = ChecklistGroupWeight::where('idchecklist', $request->idchecklist)->whereIn('idparent', $request->groups)->pluck('group')->toArray();

            $allGroups = array_merge($children, $request->groups);

            $options = $options->whereIn('group', $allGroups);
        }

        $dataGroups = $options->groupBy('idchecklistoption');

        foreach ($dataGroups as $group)
        {
            $compliance = $this->getCompliance($group);

            $item = ["idgroup" => $group[0]->group, "group" => $headers->firstWhere('group', $group[0]->group)->name, "name" => $group[0]->name, "compliance" => round($compliance, 2)];

            $resultGroups->push($item);
        }

        return $resultGroups;
    }

    private function getAllData($request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        $spots = $this->spot->getSpotWithChidrens((array) $request->idspot, true);

        $data = DB::table('wh_ticket_checklist as tc')
                  ->join('wh_ticket as t', 't.id', '=', 'tc.idticket')
                  ->where('tc.idchecklist', $request->idchecklist)
                  ->whereBetween('t.created_at', [$start, $end])
                  ->whereNull('t.deleted_at')
                  ->when(isset($request->idspot), function ($query) use ($spots) {
                    $query->whereIn('t.idspot', $spots);
                  })
                  ->when($request->has("tickets"), function ($query) use ($request) {
                    $query->whereIn('tc.idticket', $request->tickets);
                  })
                  ->select('t.idspot', 'tc.idticket', DB::raw('REPLACE(REPLACE(tc.options, "[", ""), "]", "") as options '))
                  ->get();

        $countChecklist = $data->count();

        if($data->count() == 0) return collect([]);

        $json_data = "[";

        $countChecklist = $data->count();

        for ($i = 0; $i < $data->count(); $i++)
        { 
            $json_data .= $data[$i]->options;

            if( $i == $countChecklist -1 ) {
                $json_data .= ']';
            } else {
                $json_data .= ',';
            }
        }

        return collect(json_decode($json_data));
    }

    private function getCompliance($collection)
    {
        $total = $collection->count();

        $totalYes = $collection->where('value', 1)->count();

        $compliance = ($totalYes  / $total)  * 100;

        return $compliance;
    }
}
