<?php

namespace App\Repositories\Reports;
use Illuminate\Support\Facades\DB;
use App\Models\Spot;
use Carbon\Carbon;
use Session;

class ReportTaskAverageRepository
{

    public function getAverageReport($request)
    {     
        $result = array();
        $branches =  Spot::where('isbranch', true)
                            ->get(['id', 'name', 'isbranch']);

        foreach($branches as $spot)
        {
            $spotData = $this->getReportData($request, $spot->id);

            $spotData->id = $spot->id;
            $spotData->name = $spot->name;
            $spotData->finish = intval($spotData->finish);
            $spotData->pendint = intval($spotData->pendint);
            $spotData->ReIni = intval($spotData->ReIni);
            $spotData->IniFin = intval($spotData->IniFin);
            $spotData->ReFin = intval($spotData->ReFin);
            // dd($spotData);
            array_push($result,$spotData);
        }

        return $result;
    }

    private function getReportData($request, $idspot)
    {     
        $spot  = new Spot;
        $spots = $spot->getSpotWithChidrens((array)$idspot);

        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        $ticketReport = DB::table("wh_ticket as t")
                ->select(
                    DB::raw('Sum(if(t.idstatus = 4, 1, 0)) as `finish`'),
                    DB::raw('Sum(if(t.idstatus = 1, 1, 0)) as `pendint`'),
                    DB::raw('count(t.id) as `total`'),
                    DB::raw('(ROUND(Sum(TIMESTAMPDIFF(second, t.created_at, t.startdate))  /count(t.id))) as `ReIni`'), //Pendiente - Iniciado
                    DB::raw('(ROUND(Sum(TIMESTAMPDIFF(second, t.startdate, t.finishdate)) /count(t.id))) as `IniFin`'),//Iniciado  - Finalizado
                    DB::raw('(ROUND(Sum(TIMESTAMPDIFF(second, t.created_at, t.finishdate)) /count(t.id))) as `ReFin`') //Pendiente - Finalizado
                )
                ->WhereNull('t.deleted_at')
                ->whereIn('t.idspot', $spots)
                ->whereBetween('t.created_at', [$start, $end])
                ->first();

      return $ticketReport;      
    }

}