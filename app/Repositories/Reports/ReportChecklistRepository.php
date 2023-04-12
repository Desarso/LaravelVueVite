<?php

namespace App\Repositories\Reports;
use App\Repositories\Cleaning\CleaningPlanRepository;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use App\Models\TicketChecklist;
use App\Models\Item;
use App\Models\User;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Enums\TicketStatus;
use Session;
use App\Mail\AuditMail;
use Illuminate\Support\Facades\Mail;

class ReportChecklistRepository
{
    public function getData3($request)
    {
        $checklistData = DB::table('wh_checklist_data')->get(['id', 'data']);

        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        DB::statement('SET SESSION group_concat_max_len = 18446744073709551615;');

        $data = DB::table('wh_ticket_checklist as tc')
                  ->join('wh_ticket as t', 't.id', '=', 'tc.idticket')
                  ->when(!is_null($request->idspot), function ($query) use ($request) {
                    return $query->where('t.idspot', $request->idspot);
                  })
                  ->when(!is_null($request->iduser), function ($query) use ($request) {
                    return $query->join('wh_ticket_user as tu', 'tu.idticket', '=', 't.id')
                                 ->where('tu.iduser', $request->iduser);
                  })
                  ->where('tc.idchecklist', $request->idchecklist)
                  ->whereBetween('t.created_at', [$start, $end])
                  ->whereNull('t.deleted_at')
                  ->select('tc.idchecklist', DB::raw('concat("[", GROUP_CONCAT(REPLACE(REPLACE(tc.options, "[", ""), "]", "")) ,"]") as options'))
                  ->groupBy('tc.idchecklist')
                  ->get();

        if ($data->count() == 0) return [];

        $collection = collect(json_decode($data[0]->options));

        $collection->whereIn('optiontype', [1, 2, 5])->map(function ($option) use($checklistData) {
            $this->formatData($option, $checklistData);
            return $option;
        });

        return $collection;//->sortBy('position')->values();
    }

    public function getData2($request)
    {
        $checklistData = DB::table('wh_checklist_data')->get(['id', 'data']);

        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        $data = DB::table('wh_ticket_checklist as tc')
                  ->join('wh_ticket as t', 't.id', '=', 'tc.idticket')
                  ->when(!is_null($request->idspot), function ($query) use ($request) {
                    return $query->where('t.idspot', $request->idspot);
                  })
                  ->when(!is_null($request->iduser), function ($query) use ($request) {
                    return $query->join('wh_ticket_user as tu', 'tu.idticket', '=', 't.id')
                                 ->where('tu.iduser', $request->iduser);
                  })
                  ->where('tc.idchecklist', $request->idchecklist)
                  ->whereBetween('t.created_at', [$start, $end])
                  ->whereNull('t.deleted_at')
                  ->select('tc.idticket', 'tc.options', 'tc.created_at')
                  ->get();

        $result = "";

        foreach ($data as $checklist)
        {
            $header = $this->getHeader($checklist);

            $test = str_replace("[", $header, $checklist->options);

            $result .= $test;
        }

        $result = str_replace("[", "", $result);
        $result = str_replace("]", "", $result);
        $result = str_replace("}{", "},{", $result);
 
        $result = "[" . $result . "]";
        
        $test2 = json_decode($result);

        return $test2;
    }

    public function getData($request)
    {
        $checklistData = DB::table('wh_checklist_data')->get(['id', 'data']);

        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        $data = DB::table('wh_ticket_checklist as tc')
                  ->join('wh_ticket as t', 't.id', '=', 'tc.idticket')
                  ->when(!is_null($request->idspot), function ($query) use ($request) {
                    return $query->where('t.idspot', $request->idspot);
                  })
                  ->when(!is_null($request->iduser), function ($query) use ($request) {
                    return $query->join('wh_ticket_user as tu', 'tu.idticket', '=', 't.id')
                                 ->where('tu.iduser', $request->iduser);
                  })
                  ->where('tc.idchecklist', $request->idchecklist)
                  ->whereBetween('t.created_at', [$start, $end])
                  ->whereNull('t.deleted_at')
                  ->select('tc.idticket', 'tc.options', 'tc.created_at')
                  ->get();

        $result = [];

        foreach ($data as $checklist)
        {
            $options = json_decode($checklist->options);

            $header = $this->getHeader($checklist->idticket);

            array_unshift($options, $header);

            $this->mergeData($result, $options);
        }

        $collection = collect($result);

        $collection->whereIn('optiontype', [1, 2, 5])->map(function ($option) use($checklistData) {
            $this->formatData($option, $checklistData);
            return $option;
        });

        return $collection;
    }

    private function mergeData(&$array1, &$array2)
    {
        foreach($array2 as $i) {
            $array1[] = $i;
        }
    }

    private function getHeader($idticket)
    {
        $ticket = Ticket::find($idticket);

        $title = $ticket->spot->name . " | " . $ticket->createdBy->fullname . " | " . $ticket->created_at->format('d-m-Y');

        return (object) array("name" => $title, "value" => "", "optiontype" => 6);
    }

    public function formatData($option, $checklistData)
    {
        switch ($option->optiontype)
        {
            case 1:
                $option->value = ($option->value == 1 ? "Si" : "No");
                break;

            case 2:
                $option->value = $this->getTextFromJson($option->value, $option->iddata, $checklistData);
                break;

            case 5:
                $option->value = $this->getTextFromJson($option->value, $option->iddata, $checklistData);
                break;
        }

        return $option;
    }

    public function formatApproved($option, $checklistData)
    {

        switch ($option->approved) {

            case 'null':
                $option->approved = '';
                break;

            case '1':
                $option->approved = 'Sí';
                break;

            case '0':
                $option->approved = 'No';
                break;
        }

        return $option;
    }

    private function getTextFromJson($value, $iddata, $checklistData)
    {
        if(is_null($value) || $value == "null" || is_null($iddata)) return "";

        $result = json_decode($checklistData->firstWhere('id', $iddata)->data);

        $data = collect($result);

        return $data->firstWhere('value', $value)->text;
    }


    public function getDataChecklistAudit($request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();
    
        $data = TicketChecklist::select('id', 'idticket', 'idchecklist', 'idevaluator', 'email_sent', 'created_at')
                      ->with('ticket:id,code,idstatus,idspot,created_by')
                      ->whereBetween('created_at', [$start, $end])
                      ->whereHas('ticket', function ($query) use($request){
                        $query->when(!is_null($request->idspot), function ($query) use($request){
                            return $query->where('idspot', $request->idspot);
                        })
                        ->when(!is_null($request->iduser), function ($query) use($request){
                            return $query->where('created_by', $request->iduser);
                        });
                      })
                      ->when(!is_null($request->idchecklist), function ($query) use($request){
                          return $query->where('idchecklist', $request->idchecklist);
                      })
                      ->latest()
                      ->get();
        
        return $data;
    }

    public function getChecklistDetail($request)
    {
        $ticketChecklist = TicketChecklist::find($request->id);

        $checklistData = DB::table('wh_checklist_data')->get(['id', 'data']);

        $collection = collect(json_decode($ticketChecklist->options));

        foreach($collection as $option)
        {
            $this->formatData($option, $checklistData);
        }
        
        return $collection->sortBy('position')->values();
    }

    public function sendEmailAudit($id = null)
    {
        $url = str_replace('http', 'https', env('APP_URL')) . '/task-resume?idticket=';

        $checklists = TicketChecklist::when(is_null($id), function ($query) use ($id) {
                                        return $query->whereDate('created_at', Carbon::today())
                                                     ->where('email_sent', 0)
                                                     ->whereHas('ticket', function ($q) {
                                                        $q->where('idstatus', 4);
                                                     })
                                                     ->whereHas('checklist', function ($q) {
                                                        $q->where('send_by_email', true);
                                                     });
                                     }, function ($query) use ($id) {
                                        return $query->where('id', $id);
                                     })
                                     ->get();

        $teams = DB::table('wh_team')->whereNotNull("emails")->get(["id", "emails"]);

        $checklistData = DB::table('wh_checklist_data')->get(['id', 'data']);

        foreach($checklists as $checklist)
        {
            $checklist->auditor = $checklist->ticket->createdby->fullname;

            $collection = collect(json_decode($checklist->options));

            $collection = $collection->whereNotNull('departments');

            $collection->each(function ($option, $key) use($checklistData){
                $option->last_value = $option->value;
                $this->formatData($option, $checklistData);
                //$this->getTicketIdFromUUID($option);
            });

            foreach($teams as $team)
            {
                $options = $collection->filter(function ($option, $key) use($team, $checklistData){
                    return in_array($team->id, json_decode($option->departments)) == true;
                });

                if($options->count() > 0)
                {
                    $checklist->average = $this->getAverage($options);

                    $emails = explode(",", $team->emails);

                    Mail::to($emails)->send(new AuditMail($checklist, $options->sortBy('position'), $url));

                    $checklist->increment('email_sent');
                }
            }
        }
    }

    private function getAverage($collection)
    {
        $collection = $collection->whereIn('optiontype', [1, 2]);
        
        $checked  = $collection->groupBy('last_value');

        $keys = $checked->keys()->toArray();

        $total_yes = in_array(1, $keys)  ? $checked[1]->count() : 0;
        $total_na  = in_array(2, $keys)  ? $checked[2]->count() : 0;

        $total = $collection->count() - $total_na;

        if($total == 0) return 0;
        
        return round(($total_yes / $total) * 100);
    }

    private function getTicketIdFromUUID($option)
    {
        if (!is_null($option->reportTask)) {
            
            $ticket = Ticket::where('uuid', $option->reportTask)->first(['id']);
            $option->reportTask = $ticket->id;
        }
    }
}