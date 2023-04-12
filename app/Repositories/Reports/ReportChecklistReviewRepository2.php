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

class ReportChecklistReviewRepository2
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

            $options = $options->where('optiontype', '=', 2);

            $item = ["idchecklist" => $checklist->idchecklist, "idticket" => $checklist->idticket, "idspot" => $checklist->ticket->idspot, "compliance" => round($this->getCompliance($options), 2), "iduser" => $checklist->ticket->created_by, "created_at" => $checklist->created_at];

            $result->push($item);
        }

        return $result;
    }

    public function getDataSpot($request)
    {
        $options = $this->getAllData($request);

        $options = $options->when($request->has('idchecklistoption') == true, function ($collection) use ($request) {
            return $collection->whereIn('idchecklistoption', $request->idchecklistoption);
        });

        $checklistData = DB::table('wh_checklist_data')->get(['id', 'data']);

        $options = $options->where('optiontype', '=', 2);

        $result = collect();

        $groups = $options->groupBy('idbranch');

        foreach ($groups as $spotKey => $group)
        {
            $dataGroups = $group->groupBy('iddata');

            $dataResult = collect();

            foreach ($dataGroups as $key => $dataGroup)
            {
                $totalCollection = $dataGroup->count();

                $options2 = $this->getChecklistData($key, $checklistData);

                foreach ($options2 as $option)
                {
                    $totalGroup = $dataGroup->where('value', (string)$option->value)->count();
        
                    $percent = ($totalGroup  / $totalCollection)  * 100;
                    
                    $item = ["text" => $option->text, "key" => $option->value, "count" => $totalGroup, "percent" => round($percent, 2), "color" => $this->getColor($option)];
        
                    $dataResult->push($item);
                }
            }

            $item = ["idspot" => $spotKey, "percentages" => $dataResult];
        
            $result->push($item);
        }

        return $result;
    }

    public function getDataOption($request)
    {
        $options = $this->getAllData($request);

        $options = $options->when($request->has('idchecklistoption') == true, function ($collection) use ($request) {
            return $collection->whereIn('idchecklistoption', $request->idchecklistoption);
        });

        $checklistData = DB::table('wh_checklist_data')->get(['id', 'data']);

        $options = $options->where('optiontype', '=', 2);

        $result = collect();

        $groups = $options->groupBy('idchecklistoption');

        foreach ($groups as $group)
        {
            $percentages = $this->getPercentByValue($group, $checklistData);

            $item = ["idchecklistoption" => $group[0]->idchecklistoption, "idgroup" => $group[0]->group, "name" => $group[0]->name, "percentages" => $percentages];

            $result->push($item);
        }

        return $result;
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
            $options = str_replace(', "properties": "{\"value\": \"null\"}"', '', $data[$i]->options);

            $options = str_replace('"optiontype":', '"idbranch": ' . $data[$i]->idspot . ', "optiontype":', $options);

            $json_data .= $options;

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

        $totalYes = $collection->where('value', '!=', '')->count();

        $compliance = ($totalYes  / $total)  * 100;

        return $compliance;
    }

    private function getPercentByValue($collection, $checklistData)
    {
        $result = collect();

        $totalCollection = $collection->count();

        $options = $this->getChecklistData($collection[0]->iddata, $checklistData);

        foreach ($options as $key => $option)
        {
            $totalGroup = $collection->where('value', (string)$option->value)->count();

            $percent = ($totalGroup  / $totalCollection)  * 100;
            
            $item = ["text" => $option->text, "key" => $option->value, "count" => $totalGroup, "percent" => round($percent, 2), "color" => $this->getColor($option)];

            $result->push($item);
        }

        return $result;

    }

    private function getChecklistData($iddata, $checklistData)
    {
        $result = json_decode($checklistData->firstWhere('id', $iddata)->data);

        return collect($result);
    }

    private function getColor($object)
    {
        if(property_exists($object, 'color')) return $object->color;

        return "#28c76f";
    }
}
