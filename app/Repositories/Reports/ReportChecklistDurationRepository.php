<?php

namespace App\Repositories\Reports;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TicketChecklist;
use Carbon\Carbon;
use App\Helpers\Helper;
use Session;

class ReportChecklistDurationRepository
{
    public function getData($request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();
    
        $data = TicketChecklist::select('id', 'idticket', 'idchecklist', 'created_at')
                               ->with('ticket:id,code,idstatus,idspot,startdate,finishdate,created_by')
                               ->whereBetween('created_at', [$start, $end])
                               ->when(!is_null($request->idchecklist), function ($query) use($request){
                                  return $query->where('idchecklist', $request->idchecklist);
                               })
                               ->has('ticket')
                               ->latest()
                               ->get();
        
        return $data;
    }

    public function getDataDetail($request)
    {
        $ticketChecklist = TicketChecklist::find($request->id);

        $collection = collect(json_decode($ticketChecklist->options));

        $collection = $collection->whereIn('optiontype', [6, 14]);

        $result = collect();

        foreach($collection as $option)
        {
            $item = ["name" => $option->name, "value" => $option->value, "optiontype" => $option->optiontype, "startdate" => null, "finisdate" => null, "duration" => null, "position" => $option->position];

            if(($option->optiontype == 14) && property_exists($option, 'properties') && (!is_null($option->properties)))
            {
                $properties = json_decode($option->properties);

                $item["startdate"]  = $this->formatDate($properties->startdate);
                $item["finishdate"] = $this->formatDate($properties->finishdate);
                $item["duration"]   = $properties->duration;
            }

            $result->push($item);
        }

        return $result->sortBy('position')->values();
    }

    private function formatDate($value) 
    {     
        if(!is_null($value))
        {
            return Carbon::parse($value)->setTimezone(Session::get('local_timezone'));
        } 

        return null;
    }
}