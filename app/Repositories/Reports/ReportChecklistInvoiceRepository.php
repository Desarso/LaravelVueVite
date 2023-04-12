<?php

namespace App\Repositories\Reports;
use App\Repositories\SpotRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TicketChecklist;
use Carbon\Carbon;
use App\Helpers\Helper;
use Session;

class ReportChecklistInvoiceRepository
{
    public function __construct()
    {

    }
    
    public function getData($request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        $data = TicketChecklist::when(!is_null($request->idspot), function ($query) use ($request) {
                                  return $query->whereHas('ticket', function ($q) use ($request) {
                                    $q->where('idspot', $request->idspot);
                                  });
                               })
                               ->when(!is_null($request->iduser), function ($query) use ($request) {
                                  return $query->whereHas('ticket.users', function ($q) use ($request) {
                                    $q->where('iduser', $request->iduser);
                                  });
                               })
                               ->has('ticket')
                               ->whereBetween('created_at', [$start, $end])
                               ->where('idchecklist', 3)
                               ->get();

        $result = collect();

        foreach($data as $checklist)
        {
          $options = json_decode($checklist->options);

          $options = collect($options);

          $options = $options->sortBy('position')->values()->all();

          $user = $checklist->ticket->users->first();

          $invoiceDate = (!is_null($options[1]->value) && $options[1]->value != "") ? Carbon::parse($options[1]->value) : null;

          $invoice = [
            'idspot'     => $checklist->ticket->idspot,
            'iduser'     => !is_null($user) ? $user->id : $checklist->ticket->created_by,
            'issue_date' => $options[1]->value,
            'date'       => $checklist->ticket->created_at,
            'days'       => !is_null($invoiceDate) ? $checklist->ticket->created_at->diff($invoiceDate)->days : "-----",
            'code'       => $options[2]->value,
            'supplier'   => $options[4]->value,
            'items'      => $options[3]->value,
            'amount'     => !is_null($options[5]->value) ? (int)$options[5]->value : 0
          ];

          $result->push($invoice);
        }
        
        return $result;
    }
}