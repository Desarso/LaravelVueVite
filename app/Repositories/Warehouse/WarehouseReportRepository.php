<?php

namespace App\Repositories\Warehouse;

use App\Models\Warehouse\Warehouse;
use App\Models\Spot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Repositories\SpotRepository;
use App\Models\Warehouse\WarehouseLog;

class WarehouseReportRepository
{
    protected $warehouseItemRepository;
    protected $spotRepository;
    protected $WarehouseStatus;
    protected $warehouseRepository;

    public function __construct()
    {
        $this->spotRepository = new SpotRepository;
        $this->warehouseItemRepository = new WarehouseItemRepository;
        $this->WarehouseStatus = new WarehouseStatusRepository;
        $this->warehouseRepository = new WarehouseRepository;
    }

    public function getAll($request)
    {
        $data = $this->getWarehouseReportData($request);

        $warehouseReport = $data->when($request->has('sort'), function ($query) use($request){
                        return $this->sort($query, $request->sort);
                    }, function ($query) {
                        return $query->orderBy('id', 'desc');
                    });

        $total = $warehouseReport->get()->count('id');
        $warehouses = $warehouseReport->skip($request->skip)->take($request->take)->get();
        
        return array('total' => $total, "data" => $warehouses);
    }

    public function getList()
    {
        return Warehouse::get(['id as value', 'name as text']);
    }

    public function getLast()
    {
        return DB::table('wh_warehouse')->orderBy('updated_at', 'desc')->first()->updated_at;
    }

    private function sort($query, $sorts)
    {
        foreach($sorts as $sort) {
            $query->orderBy($sort["field"], $sort["dir"]);
        }
        return $query;
    }

    public function getWarehouseReportData($request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();
        
        return Warehouse::with(['logs' =>
                    function ($query) {
                        $query->select(["idwarehouse","idstatus","created_at"])
                              ->whereIn("action",["CREATE", "CHANGE_STATUS"]);
                    }])
                    ->with('spot:id,name')
                    ->with('item:id,name')
                    ->select('id', 'oc', 'amount', 'idspot', 'iditem', 'description', 'idstatus', 'idpriority', 'iduser', 'created_at', 'updated_at')
                    ->orderBy('created_at', 'DESC')
                    ->where('idstatus', '!=', 1) //Excluye los tickets pendientes.
                    ->where('idspot', $request->idspot)
                    ->whereBetween('created_at', [$start, $end])
                    ->groupBy('id');
    }

    public function getGeneralAverage($request)
    {   
        $warehouse = $this->getWarehouseReportData($request);
        $data = $warehouse->get();
        $averages = $this->calculateAverage($data);

        return $averages;       
    }

    public function calculateAverage($warehouse)
    {
        
            $total = count($warehouse);
            $suma1 = 0;
            $suma2 = 0;
            $suma3 = 0;
            $suma4 = 0;
            $total1 = 1;
            $total2 = 1;
            $total3 = 1;
            $promedio1 = 0;
            $promedio2 = 0;
            $promedio3 = 0;
            $promedio4 = 0;

            $logs = $warehouse->where("idstatus","!=",1);

            foreach($logs as $item)
            {   
                $keys = $item->logs->keys()->toArray();

                //Pendiente-Recibido
                if(array_key_exists(0, $keys) && array_key_exists(1, $keys))
                {
                    $fecha1 = $item->logs[0]->created_at;
                    $fecha2 = $item->logs[1]->created_at;

                    $dif = $fecha1->diffInSeconds($fecha2);
                    $suma1 += $dif;
                    $total1++;
                } 
                    
                //Recibido-Generado
                if(array_key_exists(1, $keys) && array_key_exists(2, $keys))
                {
                    $fecha1 = $item->logs[1]->created_at;
                    $fecha2 = $item->logs[2]->created_at;
                
                    $dif = $fecha1->diffInSeconds($fecha2);
                    $suma2 += $dif;
                    $total2++;
                }   
                    
                //Generado-Finalizado
                if(array_key_exists(2, $keys) && array_key_exists(3, $keys))
                {
                    $fecha1 = $item->logs[2]->created_at;
                    $fecha2 = $item->logs[3]->created_at;
                    
                    $dif = $fecha1->diffInSeconds($fecha2);
                    $suma3 += $dif;
                    $total3++;
                }
            }

            if($total != 0)
            {
                $suma1 = $suma1 / 86400; 
                $suma2 = $suma2 / 86400; 
                $suma3 = $suma3 / 86400; 
                $suma4 = $suma1 + $suma2 + $suma3;
                $suma4 = Round($suma4);

                $promedio1 = Round($suma1 / $total1, PHP_ROUND_HALF_UP); //Pendiente - Recibido
                $promedio2 = Round($suma2 / $total2, PHP_ROUND_HALF_UP); //Recibido  - Generado
                $promedio3 = Round($suma3 / $total3, PHP_ROUND_HALF_UP); //Generado  - Finalizado
                $promedio4 = Round($suma4 / $total, PHP_ROUND_HALF_UP); //Generado  - Finalizado
            }

            return array(
                "total" => $total,
                "average_days" => $promedio4,
                "pending-received" => $promedio1,
                "received-generated" => $promedio2,
                "generated-finish" => $promedio3,
                // "generated-finish" => $promedio4
            );
    }
}

