<?php

namespace App\Repositories\Reports;
use App\Repositories\SpotRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TicketNote;
use App\Models\Checklist;
use Carbon\Carbon;
use App\Helpers\Helper;
use Session;
use PDF;

class ReportChecklistNoteRepository
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

        if(isset($request->idspot))
        {
            $spots = $this->spotRepository->getChildren($request->idspot);
        }
        else
        {
            $spots = json_decode(Auth::user()->spots);
        }

        $data = TicketNote::when(!is_null($request->idspot), function ($query) use($spots){
                                return $query->whereHas('ticket', function ($q) use ($spots) {
                                    $q->whereIn('idspot', $spots);
                                });
                          })
                          ->when(!is_null($request->idchecklist), function ($query) use($request){
                                return $query->whereHas('ticket.checklists', function ($q) use ($request) {
                                    $q->where('idchecklist', $request->idchecklist);
                                });
                           })
                           ->whereNotNull('idchecklistoption')
                           ->whereBetween('created_at', [$start, $end])
                           ->get();

        $result = collect();

        foreach($data as $item)
        {
            $row = (object) array(
                "idspot"     => $item->ticket->idspot,
                "spot"       => $item->ticket->spot->name,
                "option"     => $this->findOptionInJson($item->ticket->checklists[0]->options, $item->idchecklistoption),
                "note"       => $item->note,
                "type"       => $item->type,
                "created_at" => $item->created_at
            );

            $result->push($row);
        }

        return $result;
    }

    private function findOptionInJson($options, $idchecklistoption)
    {
        $options = collect(json_decode($options));

        $option = $options->firstWhere('idchecklistoption', $idchecklistoption);

        return $option->name;
    }

    public function generatePdf($request)
    {
        $data = $this->getData($request);

        $checklist = Checklist::find($request->idchecklist)->name;

        return PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
                  ->loadView('reports.report-checklist-note-pdf', ["notes" => $data, "checklist" => $checklist])
                  ->stream('test.pdf');   
    }
}