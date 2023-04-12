<?php

namespace App\Repositories\Reports;

use App\Repositories\SpotRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ChecklistData;
use App\Models\TicketNote;
use App\Models\Spot;
use Carbon\Carbon;
use App\Helpers\Helper;
use Session;

class ReportChecklistReview3Repository
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
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        $spots = $this->spot->getSpotWithChidrens((array) $request->idspot, true);

        $query = DB::table('wh_ticket as t')
            ->join('wh_ticket_checklist as tc', 't.id', '=', 'tc.idticket')
            ->where('tc.idchecklist', $request->idchecklist)
            ->whereBetween('t.created_at', [$start, $end])
            ->when(!is_null($request->id), function ($q) use ($request) {
                return $q->where('tc.id', $request->id);
            })
            ->when(isset($request->idspot), function ($query) use ($spots) {
                $query->whereIn('t.idspot', $spots);
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

        foreach ($data as $key => $item) {
            $collect = collect(json_decode($item->options));

            $object = ['id' => $item->id, 'idticket' => $item->idticket, 'iduser' => $item->created_by, 'created_at' => $item->created_at, 'percentage' => $this->getCompliancePercentage($collect)];

            $result->push($object);
        }

        return $result;
    }

    public function getDataChecklistReviewDetail($request)
    {
        if (!isset($request->selectedTicket)) {
            return [];
        }

        $query = $this->getData($request);
        $item = $query->select('tc.id', 'tc.options')
            ->where('t.id', $request->selectedTicket)
            ->first();

        $data = collect(json_decode($item->options));

        $headers = $data->where('optiontype', 6)->unique('idchecklistoption');

        $options = $data->where('optiontype', '!=', 6)->groupBy('idchecklistoption');
        // $options = $options->sortBy('position');

        $result = collect();

        foreach ($options as $option) {
            $header = $this->getHeader($headers, $option);

            $object = [
                'name' => $option[0]->name, 
                'section' => $header, 
                'value' => $this->getValue($option[0]),
            ];

            $result->push($object);
        }

        return $result;
    }

    public function getDataChecklistReviewNotes($request)
    {
        if (!isset($request->selectedTicket)) {
            return [];
        }

       $notes = DB::table('wh_ticket_note as tn')
                    ->where('tn.idticket', $request->selectedTicket)
                    ->join('wh_checklist_option as co', 'tn.idchecklistoption', '=', 'co.id')
                    ->select('tn.id', 'tn.note', 'tn.type', 'tn.created_by','tn.created_at', 'co.name AS line')
                    ->get();
        
        foreach ($notes as $note) {

            if ($note->type == 2) {
                $note->note = '<img src="'.$note->note.'" width="150" height="150">';
            }
        }

        return $notes;
    }


    private function getCompliancePercentage($collect)
    {
        $collect = $collect->where('optiontype', '!=', 6)->where('value', '!=', '');

        $groups = $collect->countBy('value');

        if (!array_key_exists(1, $groups->toArray()) || $groups[1] == 0) return 0;

        $percentage = ($groups[1] / $collect->count()) * 100;

        return round($percentage);
    }

    private function getHeader($headers, $group)
    {
        $group = $group[0]->group;

        if (is_null($group)) return "-----";

        $header = $headers->firstWhere('group', $group);

        return (is_null($header) ? '-----' : $header->name);
    }

    public function getValue($option)
    {
        $value = "";

        switch ($option->optiontype)
        {
            case 1:
                $value = $option->value == '1' ? "Sí" :"No" ;
                break;

            case 2:
                $checklistData = ChecklistData::where('id', $option->iddata)->first();
                $data = collect(json_decode($checklistData->data))->firstWhere('value', $option->value);

                if ($data) {
                    $value = $data->text;
                }
                break;

            case 5:
                $checklistData = ChecklistData::where('id', $option->iddata)->first();
                $data = collect(json_decode($checklistData->data));
                $value = $data->firstWhere('value', $value)->text;
                break;

            case 11:
                if ($option->value != '' && !is_null($option->value)) {
                    $value = '<img src="'.$option->value.'" width="150" height="150">';
                }
                break;

            case 12:
                if ($option->value != '' && !is_null($option->value)) {
                    $value = '<img src="'.$option->value.'" width="150" height="150">';
                }
                break;
            
            default:
                $value = $option->value;
                break;
        }

        return $value;
    }
}
