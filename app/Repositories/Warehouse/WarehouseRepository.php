<?php

namespace App\Repositories\Warehouse;


use App\Models\Warehouse\Warehouse;
use App\Models\Spot;
use App\Repositories\ConfigRepository;
use App\Repositories\ItemRepository;
use App\Repositories\SpotRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Session;
use Carbon\Carbon;
use App\Enums\App;

class WarehouseRepository
{
    protected $warehouseItemRepository;
    protected $spotRepository;
    protected $WarehouseStatus;

    public function __construct()
    {
        $this->spotRepository = new SpotRepository;
        $this->warehouseItemRepository = new WarehouseItemRepository;
        $this->WarehouseStatus = new WarehouseStatusRepository;
    }

    public function getAll($request)
    {
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        $spots = json_decode(Auth::user()->spots);

        $warehouse = Warehouse::with('item:id,name')
                              ->withCount('notes')
                              ->whereBetween('created_at', [$start, $end])
                              ->whereIn('idspot', $spots)
                              ->when(!is_null($request->idstatus), function ($query) use ($request) {
                                return $query->where('idstatus', $request->idstatus);
                              })
                              ->when(!is_null($request->idspot), function ($query) use ($request) {
                                return $query->where('idspot', $request->idspot);
                              })
                              ->when(!is_null($request->idsupplier), function ($query) use ($request) {
                                return $query->where('idsupplier', $request->idsupplier);
                              })
                              ->when(!is_null($request->iditem), function ($query) use ($request) {
                                return $query->where('iditem', $request->iditem);
                              })
                              ->when(!is_null($request->iduser), function ($query) use ($request) {
                                return $query->where('iduser', $request->iduser);
                              })
                              ->when($request->has('sort'), function ($query) use($request){
                                return $this->sort($query, $request->sort);
                              }, function ($query) {
                                return $query->orderBy('id', 'desc');
                              });

        $total = $warehouse->count('id');

        $warehouses = $warehouse->skip($request->skip)->take($request->take)->latest()->get();
        
        return array('total' => $total, "data" => $warehouses);
    }

    public function getList()
    {
        return Warehouse::get(['id as value', 'name as text']);
    }

    public function getLast()
    {
        $last = DB::table('wh_warehouse')->orderBy('updated_at', 'desc')->first();

        return (is_null($last) ? "null" : $last->updated_at);
    }

    public function nextStatus($request)
    {
      $warehouse = Warehouse::with("status")->find($request->id);

      $statuses = DB::table('wh_warehouse_status')->whereIn('id', json_decode($warehouse->status->nextstatus))->get(['id', 'name', 'color']);

      return $statuses;
    }   

    public function changeStatus($request)
    {
      $warehouse = Warehouse::with("status")->find($request->id);
      $warehouse->idstatus = $request->idstatus;
      $warehouse->save();

      return response()->json(['success' => true]);
    }   
    
    public function create($request)
    {
        $rows = $request->models;

        foreach($rows as $key => $row)
        {
            $rows[$key]['iduser'] = Auth::id();

            Warehouse::create($rows[$key]);
        }

        return response()->json(['success' => true]);
    }

    public function update($request)
    {
        $model = Warehouse::find($request->id);
        $model->fill($request->all())->save();
        return $model;
    }
    
    public function delete($request)
    {
        $model = Warehouse::findOrFail($request->id);
        $model->delete();
    }
    
    private function sort($query, $sorts)
    {
        foreach($sorts as $sort) {
            $query->orderBy($sort["field"], $sort["dir"]);
        }
        return $query;
    }

    public function getSettings() 
    {
        $settings = DB::table('wh_app')->where('id', App::Warehouse)->first()->settings;
        return json_decode($settings);
    }


    /********** APP`s functions **************/
    
     public function saveWarehouseRequestAPP($request)
     {
        Auth::loginUsingId($request->iduser);
        Warehouse::create($request->all());

        return response()->json([ 
            'success' => true, 
            'message' => 'Acción completada con éxito'
        ]);
     }


     public function getWarehouseRequestAPP($request)
     {  
        $warehouse = Warehouse::select('id', 'idstatus', 'idpriority', 'iditem', 'iduser', 'idspot', 'oc', 'amount', 'description', 'created_at')
                            ->with('status:id,name,color')
                            ->with('priority:id,name')
                            ->with('spot:id,name')
                            ->with('user:id,firstname,lastname,urlpicture')
                            ->with('item:id,name')
                            ->orderBy('id', 'desc')
                            ->get();

        return $warehouse;
     }
}//Fin Repository