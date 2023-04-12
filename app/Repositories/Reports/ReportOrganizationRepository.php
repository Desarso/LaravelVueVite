<?php

namespace App\Repositories\Reports;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Spot;
use App\Models\Checklist;
use App\Helpers\Helper;
use App\Enums\TicketStatus;
use Session;
use stdClass;

class ReportOrganizationRepository
{
    public function getChecklistGroupBySpot($request)
    {
        if (is_null($request->idchecklist)) return [];

        $checklist_expected = $this->getChecklistExpected($request);

        $spots = array();

        if (isset($request->idspot)) {
            $spot  = new Spot;
            $spots = $spot->getSpotWithChidrens((array) $request->idspot, true);
        }

        $data = DB::table('wh_spot as s')
            ->join('wh_ticket as t', 't.idspot', '=', 's.id')
            ->join('wh_ticket_checklist as tc', 'tc.idticket', '=', 't.id')
            ->where('s.isbranch', 1)
            ->where('tc.idchecklist', $request->idchecklist)
            ->when(isset($request->idspot), function ($query) use ($spots) {
                return $query->whereIn('t.idspot', $spots);
            })
            ->when(isset($request->iduser), function ($query) use ($request) {
                return $query->join('wh_ticket_user as tr', 'tr.idticket', '=', 't.id')
                             ->where('tr.iduser', $request->iduser);
            })
            ->whereBetween('t.created_at', $this->getRangeDate($request))
            ->whereNull('t.deleted_at')
            ->select('s.id', 's.name', 't.created_at', 'tc.results', 'tc.options', 'tc.idevaluator', 'tc.idticket')
            ->get();

            
        $data = $data->groupBy('id');
        $branches = collect();
        $result   = array();

        foreach ($data as $item) {
            $missing_lines = 0;

            if ($item->count() < $checklist_expected) {
                $count_lines = DB::table('wh_checklist_option')
                    ->where('idchecklist', $request->idchecklist)
                    ->when(!is_null($request->group), function ($query) use ($request) {
                        return $query->where('group', $request->group);
                    })
                    ->when((int) $request->showinreport, function ($query) use ($request) {
                        return $query->where('showinreport', true);
                    })
                    ->where('optiontype', '!=', 6)
                    ->count();

                $missing_lines = ($checklist_expected - $item->count()) * $count_lines; //Se incluyen el total de líneas de los checklist que no se hicieron.
            }

            foreach ($item as $data_options) {
                $result = array_merge($result, json_decode($data_options->options));
            }

            $options = collect($result);

            $options = $options->when(!is_null($request->group), function ($result) use ($request) {
                                    return $result->where('group', $request->group);
                               })
                               ->when((int) $request->showinreport, function ($result){
                                    return $result->where('showinreport', true);
                               });

            $options = $options->where('optiontype', '!=', 6);

            $results = $this->countLines($options, $missing_lines);

            $brach = [
                'idspot'             => $item[0]->id,
                'name'               => $item[0]->name,
                'average_completed'  => $results['average_completed'],
                'average_verified'   => $results['average_verified'],
                'checked_true'       => $results['checked_true'],
                'total_checked'      => $results['total_checked'],
                'approved_true'      => $results['approved_true'],
                'approved_false'     => $results['approved_false'],
                'total_approved'     => $results['total_approved'],
                'applied_checklist'  => [$item->count(), $checklist_expected],
                'applied_evaluation' => $item->where('idevaluator', '!=', null)->count(),
                'evaluation_codes'   => $item->whereNotNull('idevaluator')->pluck('idticket')->join(', ')
            ];

            $branches->push($brach);
            $result = [];
        }

        return $branches->sortByDesc('average_completed')->values();
    }

    public function getChecklistBranchReportBySection($request)
    {
        if (is_null($request->idchecklist)) return [];

        $data = $this->getDataChecklist($request);

        $result = $data["data"];

        $headers = $result->where('optiontype', 6)->unique('idchecklistoption');

        $result = $result->where('optiontype', '!=', 6);

        $grouped = $result->groupBy('idchecklistoption');

        $options = collect();

        $missing_lines = 0;

        $checklist_expected = ($this->getChecklistExpected($request) * $data["count_branches"]);

        if ($data["count_checklist"] < $checklist_expected) {
            $missing_lines = ($checklist_expected - $data["count_checklist"]);
        }

        foreach ($grouped as $group) {
            $results = $this->countLines($group, $missing_lines);

            $header = $headers->firstWhere('group', $group[0]->group);

            $option = [
                'group'             => is_null($header) ? "-------" : $header->group,
                'header'            => is_null($header) ? "-------" : $header->name,
                'name'              => $group[0]->name,
                'average_completed' => $results['average_completed'],
                'average_verified'  => $results['average_verified'],
                'ticket_not_checked' => $results['ticket_not_checked']
            ];

            $options->push($option);
        }

        return $options->sortBy('group')->values();
    }

    public function getDataChecklist($request)
    {
        $spots = array();

        if (isset($request->idspot)) {
            $spot  = new Spot;
            $spots = $spot->getSpotWithChidrens((array) $request->idspot, true);
        } else {
            $spots = DB::table('wh_spot')->where('isbranch', 1)->select('id')->pluck('id')->toArray();
        }

        $data = DB::table('wh_ticket_checklist as tc')
            ->join('wh_ticket as t', 't.id', '=', 'tc.idticket')
            ->when(isset($request->idspot), function ($query) use ($spots) {
                return $query->whereIn('t.idspot', $spots);
            })
            ->when(isset($request->iduser), function ($query) use ($request) {
                return $query->join('wh_ticket_user as tr', 'tr.idticket', '=', 't.id')
                             ->where('tr.iduser', $request->iduser);
            })
            ->where('tc.idchecklist', $request->idchecklist)
            ->whereBetween('t.created_at', $this->getRangeDate($request))
            ->whereNull('t.deleted_at')
            ->select('idticket', DB::raw('REPLACE(REPLACE(tc.options, "[", ""), "]", "") as options '))
            ->get();
            
        $json_data = "[";
        $count_checklist = $data->count();

        for ($i = 0; $i < $data->count(); $i++) { 

            $options = str_replace(', "properties": "{\"value\": \"0\", \"required\": false}"', '', $data[$i]->options);

            $options = str_replace("{", '{"idticket": ' . $data[$i]->idticket . ', ', $options);

            $json_data .= $options;

            if( $i == $count_checklist -1 ) {
                $json_data .= ']';
            } else {
                $json_data .= ',';
            }
        }
        
        if ($count_checklist == 0) return ["data" => collect(), "count_checklist" => 0, "count_branches" => 0];

        $data = collect(json_decode($json_data));

        $data = $data->when(!is_null($request->group), function ($result) use ($request) {
                        return $result->where('group', $request->group);
                     })
                     ->when((int) $request->showinreport, function ($result){
                        return $result->where('showinreport', true);
                     });

        return ["data" => $data, "count_checklist" => $count_checklist, "count_branches" => count($spots)];
    }

    public function getChecklistBranchReport($request)
    {
        if (is_null($request->idchecklist)) return [];

        $data = $this->getDataChecklist($request);

        $result = $data["data"];

        $checklist_expected = ($this->getChecklistExpected($request) * $data["count_branches"]);

        $result = $this->checkHeader($result);

        $headers = $result->where('optiontype', 6)->unique('idchecklistoption');

        $result = $result->where('optiontype', '!=', 6);

        $grouped = $result->groupBy('group');

        $options = collect();

        foreach ($grouped as $group)
        {
            $missing_lines = 0;

            if ($data["count_checklist"] < $checklist_expected) {

                $count_lines = DB::table('wh_checklist_option')
                                ->where('idchecklist', $request->idchecklist)
                                ->where('group', $group[0]->group)
                                ->where('optiontype', '!=', 6)
                                ->when((int) $request->showinreport, function ($result) {
                                    return $result->where('showinreport', true);
                                 })
                                ->count();

                $missing_lines = ($checklist_expected - $data["count_checklist"]) * $count_lines; //Se incluyen el total de líneas de los checklist que no se hicieron.
            }

            $results = $this->countLines($group, $missing_lines);

            $header = $headers->firstWhere('group', $group[0]->group);

            $option = [
                'group_id'          => $group[0]->group,
                //'group_name'      => $headers->firstWhere('group', $group[0]->group)->name,
                'group_name'        => is_null($header) ? "-------" : $header->name,
                'average_completed' => $results['average_completed'],
                'average_verified'  => $results['average_verified'],
                'checked_true'      => $results['checked_true'],
                'total_checked'     => $results['total_checked'],
                'approved_true'     => $results['approved_true'],
                'approved_false'    => $results['approved_false'],
                'total_approved'    => $results['total_approved']
            ];
            $options->push($option);
        }

        return $options;
    }

    public function getRangeDate($request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        return [$start, $end];
    }

    public function countLines($collection, $missing_lines)
    {
        $approved = $collection->groupBy('approved');
        $checked  = $collection->groupBy('value');

        $keys1 = $approved->keys()->toArray();
        $keys2 = $checked->keys()->toArray();

        $checked_true   = in_array(1, $keys2) ? $checked[1]->count()  : 0;
        $checked_na     = in_array(2, $keys2) ? $checked[2]->count()  : 0;
        $approved_true  = in_array(1, $keys1) ? $approved[1]->count() : 0;
        $approved_false = in_array(0, array_diff($keys1, ["null"])) ? $approved[0]->count()  : 0;
        $approved_null  = in_array("null", array_diff($keys1, [0])) ? $approved["null"]->count() : 0;

        $ticket_not_checked = in_array(0, $keys2) ? $checked[0]->pluck('idticket')->toArray() : [];

        $group_total    = $collection->count();
        $total_checked  = ($group_total - $checked_na) + $missing_lines;
        $total_approved = $group_total - $approved_null;

        return [
            'checked_true'      => $checked_true,
            'total_checked'     => $total_checked,
            'approved_true'     => $approved_true,
            'approved_false'    => $approved_false,
            'total_approved'    => $total_approved,
            'average_completed' => $total_checked  > 0 ? round(($checked_true  / $total_checked)  * 100) : 0,
            'average_verified'  => $total_approved > 0 ? round(($approved_true / $total_approved) * 100) : 0,
            'ticket_not_checked' => $ticket_not_checked 
        ];
    }

    public function checkHeader($collection)
    {
        if ($collection->count() == 0) {
            return $collection;
        }

        $header = $collection->firstWhere('optiontype', 6);

        if (is_null($header)) {
            $newHeader = new stdClass();
            $newHeader->idchecklistoption = 0;
            $newHeader->name              = "General";
            $newHeader->optiontype        = 6;
            $newHeader->value             = 0;
            $newHeader->iditem            = 0;
            $newHeader->idspot            = 0;
            $newHeader->position          = 0;
            $newHeader->group             = 0;
            $newHeader->departments       = "[]";
            $newHeader->timestamp         = Carbon::now()->timestamp;
            $newHeader->approved          = "null";
            $collection->push($newHeader);
        }

        return $collection;
    }

    private function getChecklistExpected($request) {

        $model = Checklist::find($request->idchecklist);
        $checklist_expected = $model->expected_by_week;

        if (is_null($checklist_expected)) {
            $checklist_expected = $this->getWorkDays($request->start, $request->end);
        } else {
            $weeks = $this->getWorkWeeks($request->start, $request->end);
            $checklist_expected = $weeks * $checklist_expected;
        }
        
        return $checklist_expected;
    }

    public function getWorkWeeks($start, $end)
    {
        $start = Carbon::parse($start);
        $end   = Carbon::parse($end);

        $weeks = $start->diffInWeeks($end);
        if($weeks == 0) $weeks = 1;

        return $weeks;
    }

    public function getWorkDays($start, $end)
    {
        $start = Carbon::parse($start);
        $end   = Carbon::parse($end);

        $days = $start->diffInDaysFiltered(function (Carbon $date) {
            return !$date->isWeekend();
        }, $end);

        if (!$end->isWeekend()) $days++;

        return $days;
    }
}

