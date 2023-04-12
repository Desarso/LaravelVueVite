<?php

namespace App\Repositories\Reports;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Spot;
use Carbon\Carbon;
use Session;
use App\Repositories\SpotRepository;

class ReportItemRepository
{

    protected $spotRepository;

    public function __construct()
    {
        $this->spotRepository = new SpotRepository;
    }

    public function getFrequenteItemsReport($request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        $spots = json_decode(Auth::user()->spots);

        $data = DB::table('wh_ticket as t')
                    ->join('wh_item as i', 'i.id', '=', 't.iditem')
                    ->select('i.name', DB::raw('count(i.name) as total'))
                    ->whereBetween('t.created_at', [$start, $end])
                    ->where('t.byclient', $request->byclient)
                    ->when(isset($request->idteam), function ($query) use($request){
                        return $query->where('t.idteam', $request->idteam);
                    })
                    ->when(isset($request->idspot), function ($query) use ($request) {

                        $spots = $this->spotRepository->getChildren($request->idspot);

                        return $query->whereIn('t.idspot', $spots);
                    })
                    ->whereIn('idspot', $spots)
                    ->whereNull('t.deleted_at')
                    ->groupBy('i.name')
                    ->orderBy('total', 'desc')
                    ->limit(10)
                    ->get();

          return $data;
    }

    public function getTaskBySporReport($request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        $spots = json_decode(Auth::user()->spots);

        $data = DB::table('wh_ticket as t')
                    ->join('wh_spot as s', 's.id', '=', 't.idspot')
                    ->select('s.name', DB::raw('count(s.name) as total'))
                    ->whereBetween('t.created_at', [$start, $end])
                    ->when(isset($request->idteam), function ($query) use($request){
                        return $query->where('t.idteam', $request->idteam);
                    })
                    ->when(isset($request->idspot), function ($query) use ($request) {

                        $spots = $this->spotRepository->getChildren($request->idspot);

                        return $query->whereIn('t.idspot', $spots);
                    })
                    ->whereIn('idspot', $spots)
                    ->whereNull('t.deleted_at')
                    ->groupBy('s.name')
                    ->orderBy('total', 'desc')
                    ->limit(10)
                    ->get();

          return $data;
    }

}