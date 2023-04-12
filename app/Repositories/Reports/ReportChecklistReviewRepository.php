<?php

namespace App\Repositories\Reports;
use App\Repositories\SpotRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TicketNote;
use Carbon\Carbon;
use App\Helpers\Helper;
use Session;

class ReportChecklistReviewRepository
{
    protected $spotRepository;

    public function __construct()
    {
        $this->spotRepository = new SpotRepository;
    }

    public function getData($request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        $query = DB::table('wh_ticket as t')
                   ->join('wh_ticket_checklist as tc', 't.id', '=', 'tc.idticket')
                   ->where('tc.idchecklist', $request->idchecklist)
                   ->whereBetween('t.created_at', [$start, $end])
                   ->when(!is_null($request->id), function ($q) use ($request) {
                        return $q->where('tc.id', $request->id);
                   })
                   ->whereNull('t.deleted_at');

        return $query;
    }

    public function getDataChecklistReview($request)
    {
        $query = $this->getData($request);

        $result = collect();

        $data = $query->select('tc.id', 't.id as idticket', 't.created_by', 't.created_at', 'tc.options')
                      ->get();

        foreach ($data as $key => $item)
        {
            $collect = collect(json_decode($item->options));

            $object = ['id' => $item->id, 'idticket' => $item->idticket, 'created_by' => $item->created_by, 'created_at' => $item->created_at, 'percentage' => $this->getCompliancePercentage($collect)];
        
            $result->push($object);
        }
        
        return $result;
    }

    public function getDataChecklistReviewBySection($request)
    {
        $data = $this->getFullCollectionData($request);

        $headers = $data->where('optiontype', 6)->unique('idchecklistoption');

        $groups = $data->where('optiontype', '!=', 6)->groupBy('group');

        $series = array();
        $labels = array();

        foreach ($groups as $key => $group)
        {
            array_push($series, $this->getCompliancePercentage($group));
            array_push($labels, $this->getHeader($headers, $group));
        }

        $data = [["name"=> "Porcentaje", "data" => $series]];

        return ["labels" => $labels, "series" => $data];
    }

    public function getDataChecklistReviewByOption($request)
    {
        $data = $this->getFullCollectionData($request);

        $headers = $data->where('optiontype', 6)->unique('idchecklistoption');

        $groups = $data->where('optiontype', '!=', 6)->groupBy('idchecklistoption');

        $result = collect();

        foreach ($groups as $key => $group)
        {
            $header = $this->getHeader($headers, $group);

            $object = ['name' => $group[0]->name, 'section' => $header, 'percentage' => $this->getCompliancePercentage($group)];
        
            $result->push($object);
        }

        return $result;
    }

    private function getFullCollectionData($request)
    {
        $query = $this->getData($request);

        $data = $query->select('t.id', DB::raw('REPLACE(REPLACE(tc.options, "[", ""), "]", "") as options '))
                      ->get();

        $jsonString = "[";

        $total = $data->count();

        for ($i = 0; $i < $total; $i++) 
        {
            $jsonString .= $data[$i]->options;

            if($i == ($total -1))
            {
                $jsonString .= ']';
            }
            else
            {
                $jsonString .= ',';
            }
        }

        return collect(json_decode($jsonString));
    }

    private function getCompliancePercentage($collect)
    {
        $collect = $collect->where('optiontype', 2)->where('value', '!=', 2);

        $groups = $collect->countBy('value');

        if(!array_key_exists(1, $groups->toArray()) || $groups[1] == 0) return 0;

        $percentage = ($groups[1] / $collect->count()) * 100;

        return round($percentage);
    }

    private function getHeader($headers, $group)
    {
        $group = $group[0]->group;

        if(is_null($group)) return "-----";

        $header = $headers->firstWhere('group', $group);

        return (is_null($header) ? '-----' : $header->name);
    }
}