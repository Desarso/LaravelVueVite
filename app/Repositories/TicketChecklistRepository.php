<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\TicketChecklist;
use App\Models\TicketNote;
use App\Models\Ticket;
use App\Repositories\Reports\ReportChecklistRepository;
use Carbon\Carbon;
use App\Enums\App;
use PDF;
use Mail;

class TicketChecklistRepository
{
    protected $reportChecklistRepository;

    public function __construct()
    {
        $this->reportChecklistRepository = new ReportChecklistRepository;
    }

    public function get($request)
    {
        $data = DB::table('wh_checklist_data')->get();

        $notes = TicketNote::with('createdBy:id,firstname,lastname')->where('idticket', $request->idticket)->get();

        $checklist = DB::table('wh_ticket_checklist as tc')
                     ->join('wh_ticket as t', 't.id', '=', 'tc.idticket')
                     ->join('wh_checklist as c', 'c.id', '=', 'tc.idchecklist')
                     ->leftJoin('wh_user as u', 'u.id', '=', 'tc.idevaluator')
                     ->where('tc.idticket', $request->idticket)
                     ->select('t.idstatus', 't.idteam', 'tc.options', 'tc.idevaluator', DB::raw('CONCAT(u.firstname," ",u.lastname) AS evaluator'), 'u.urlpicture')
                     ->first();

        $options = collect(json_decode($checklist->options));

        $options = $options->whereNull('idparent')->map(function ($option) use ($options, $data, $notes){
            $option->data     = (is_null($option->iddata) ? null : json_decode($data->firstWhere('id', $option->iddata)->data));
            $option->notes    = $notes->where('idchecklistoption', $option->idchecklistoption)->values()->toArray();
            $option->children =  ($option->optiontype == 16 ? $this->getChildren($option, $options, $data) : []);  
            return $option;
        });

        return view('task.options', ["idstatus" => $checklist->idstatus, "options" => $options->sortBy('position'), "idevaluator" => $checklist->idevaluator, "checklist" => $checklist]);
    }

    private function getChildren($tabla, $options, $data)
    {
        $children = $options->where('idparent', $tabla->idchecklistoption);

        if($children->count() == 0) return [];

        $children->map(function ($child) use($data){
        
            if(!is_null($child->iddata))
            {
                $collect = collect(json_decode($data->firstWhere('id', $child->iddata)->data));

                $dataItem = $collect->firstWhere('value', $child->value);

                $child->value = is_null($dataItem) ? '' : $dataItem->text;
            }

            return $child;
        });

        return $children->groupBy('row');
    }

    public function save($request)
    {
        $ticketChecklist = TicketChecklist::where('idticket', $request->idticket)->first();

        $collection_options = collect(json_decode($ticketChecklist->options));

        $property = $request->has("approve") ? "approved" : "value";

        foreach($request->options as $option)
        {
            $item = $collection_options->firstWhere('idchecklistoption', $option['idchecklistoption']);
            $item->$property = $option['value'];
        }

        $ticketChecklist->options = $collection_options->toJson();
        $ticketChecklist->results = $this->getResults($collection_options);
        $ticketChecklist->save();
        $ticketChecklist->ticket->touch();

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    }

    public function getResults($options)
    {
        $collection = $options->where('optiontype', '!=', 6);
        $completed  = 0;
        $pending    = 0;

        foreach($collection as $option)
        {
            $this->isCompleted($option) == true ? $completed++ : $pending++;
        }

        return json_encode(["total" => $collection->count(), "si" => $completed, "no" => $pending]);
    }

    public function assignEvaluator($request)
    {
        $ticketChecklist = TicketChecklist::where('idticket', $request->idticket)->first();

        $ticketChecklist->idevaluator = Auth::id();
        $ticketChecklist->save();
        $ticketChecklist->ticket()->update(['approved' => 1]);
        $ticketChecklist->ticket->touch();

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    }

    public function isCompleted($option)
    {
        $result = false;

        switch($option->optiontype)
        {
            case 1:
                //CHECK
                $result = (int)$option->value == 1 ? true : false;
                break;

            case 3:
                //TEXT
                $result = strlen($option->value) > 1 ? true : false;
                break;

            case 4:
                //NUMBER
                $result = $option->value > 1 ? true : false;
                break;

            case 5:
                //SI/NO/NA
                $result = $option->value != 2 ? true : false;
                break;
        }

        return $result;

        
    }

    public function getChecklistApp($request)
    {
        return  DB::table('wh_ticket_checklist as tc')
                    ->join('wh_checklist as c', 'c.id', '=', 'tc.idchecklist')
                    ->where('tc.idticket', $request->idtask)
                    ->select('c.name', 'tc.idevaluator', 'tc.options')
                    ->get();
    }

    public function addChecklistEvaluatorApp($request) {

        $ticketChecklist = TicketChecklist::where('idticket', $request->idtask)->first();

        $ticketChecklist->idevaluator = $request->iduser;
        $ticketChecklist->save();
        $ticketChecklist->ticket->touch();

        $ticket = Ticket::findOrFail($request->idtask);
        $ticket->updated_by = $request->iduser;
        $ticket->approved = $request->approved = true;
        $ticket->save();

        return response()->json(['success' => true]);
    }

    public function getEvalutionUserChecklistAPP($request)
    {
        $checklist = DB::table('wh_ticket_checklist as tc')
                        ->select('tc.options')
                        ->where('tc.idticket', $request->id)
                        ->first();

        if (is_null($checklist)) {
            return response()->json(['success' => false]);
        } else {
            return response()->json(['success' => true, 'options' => $checklist->options]);
        }
    }

    public function synctTaskChecklistAPP($request)
    {
        $model = TicketChecklist::where('idticket', $request->id)->first();
        $model->fill(['options' => $request->checklist])->save();

        return response()->json(['success' => true]);
    }

    public function generatePdf($request)
    {
        $pdfTemplate = DB::table('wh_organization')->first()->pdf_template;

        $ticketChecklist = TicketChecklist::with([
                                                    'ticket' => function ($query) {
                                                        $query->select('id', 'name', 'idspot', 'approved', 'created_at');
                                                    },
                                                    'ticket.spot' => function ($query) {
                                                        $query->select('id', 'name');
                                                    },
                                                    'ticket.notes' => function ($query) {
                                                        $query->select('id', 'note', 'type', 'idticket', 'idchecklistoption', 'created_by')
                                                                ->whereNotNull('idchecklistoption');
                                                    },
                                                ])
                                                ->where('idticket', $request->idticket)
                                                ->first();
        
        
        $checklistData = DB::table('wh_checklist_data')->get(['id', 'data']);

        $collection = collect(json_decode($ticketChecklist->options));
        $averages = $this->getChecklistAverage($collection);

        foreach($collection as $option)
        {
            $this->reportChecklistRepository->formatData($option, $checklistData);
            $this->reportChecklistRepository->formatApproved($option, $checklistData);
            // $option->notes = $ticketChecklist->ticket->notes->where('idchecklistoption', $option->idchecklistoption);
            $option->notes = $ticketChecklist->ticket->notes->load('createdBy')->where('idchecklistoption', $option->idchecklistoption);
            $option->properties = json_decode($option->properties);
            $option->children =  ($option->optiontype == 16 ? $this->getChildren($option, $collection, $checklistData) : []);  
        }
        
        $collection = $collection->whereNull('idparent')->sortBy('position')->values();

        return PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
                  ->loadView('task.' . $pdfTemplate, ["ticketChecklist" => $ticketChecklist, "options" => $collection, "averages" => $averages])
                  ->stream('test.pdf');   
    }

    public function viewPdf($request)
    {
    
            $ticketChecklist = TicketChecklist::with([
                                                    'ticket' => function ($query) {
                                                        $query->select('id', 'name', 'idspot', 'approved', 'created_at');
                                                    },
                                                    'ticket.spot' => function ($query) {
                                                        $query->select('id', 'name');
                                                    },
                                                    'ticket.notes' => function ($query) {
                                                        $query->select('id', 'note', 'type', 'idticket', 'idchecklistoption', 'created_by')
                                                                ->whereNotNull('idchecklistoption');
                                                    },
                                                ])
                                                ->where('idticket', 85)
                                                ->first();
        
        
        $checklistData = DB::table('wh_checklist_data')->get(['id', 'data']);

        $collection = collect(json_decode($ticketChecklist->options));
        $averages = $this->getChecklistAverage($collection);

        foreach($collection as $option)
        {
            $this->reportChecklistRepository->formatData($option, $checklistData);
            $this->reportChecklistRepository->formatApproved($option, $checklistData);
            // $option->notes = $ticketChecklist->ticket->notes->where('idchecklistoption', $option->idchecklistoption);
            $option->notes = $ticketChecklist->ticket->notes->load('createdBy')->where('idchecklistoption', $option->idchecklistoption);
            $option->properties = json_decode($option->properties);
            $option->children =  ($option->optiontype == 16 ? $this->getChildren($option, $collection, $checklistData) : []);  
        }
        
        $collection = $collection->sortBy('position')->values();

        return view('task.pdf-checklist', ["ticketChecklist" => $ticketChecklist, "options" => $collection]);
    }

    public function sendPdfEmail($request)
    {
        $ticketChecklist = TicketChecklist::with([
                                                    'ticket' => function ($query) {
                                                        $query->select('id', 'name', 'idspot', 'created_at');
                                                    },
                                                    'ticket.spot' => function ($query) {
                                                        $query->select('id', 'name');
                                                    }
                                                ])
                                                ->where('idticket', $request->idticket)
                                                ->first();

        $checklistData = DB::table('wh_checklist_data')->get(['id', 'data']);

        $collection = collect(json_decode($ticketChecklist->options));
        $averages = $this->getChecklistAverage($collection);

        foreach($collection as $option)
        {
            $this->reportChecklistRepository->formatData($option, $checklistData);
            $option->notes = $ticketChecklist->ticket->notes->where('idchecklistoption', $option->idchecklistoption);
        }
        
        $collection = $collection->sortBy('position')->values();
        $email = explode(",", $request->email);
            
        $pdf = PDF::loadView('task.pdf-checklist', ["ticketChecklist" => $ticketChecklist, "options" => $collection, "averages" => $averages]);
            
        Mail::send('emails.FormTemplate', [ "comment" => $request->comment], function($message) use($ticketChecklist, $email, $pdf) {
            $message->to($email)
                    ->subject($ticketChecklist->ticket->name)
                    ->attachData($pdf->output(), ($ticketChecklist->ticket->name . ".pdf"));
        });

        return response()->json(['success' => true]);
    }

    public function getChecklistAverage($options)
    {
        $collection = $options->where('optiontype', '!=', 6);
        $completed  = 0;
        $approved   = 0;

        foreach($collection as $option)
        {
            if($this->isCompleted($option) == true) $completed++;

            if (!is_null($option->approved)) {
                if($option->approved == "1") $approved++;
            }
        }

        $average = $completed / $collection->count();
        $average = $average * 100;

        if ($approved != 0) {
            $approved = $approved / $collection->count();
            $approved = $approved * 100;
        }

        return ["average" => $average, "approved" => $approved];
    }
}