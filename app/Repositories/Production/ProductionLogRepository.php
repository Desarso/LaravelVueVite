<?php

namespace App\Repositories\Production;
use Illuminate\Support\Facades\DB;
use App\Models\Production\ProductionLog;
use Carbon\Carbon;
use Session;

class ProductionLogRepository
{
    public function getAll()
    {
        return ProductionLog::get( ["id","name", "type","idstop","idstatus", "created_by","iduser","idproduction","idequipment", "started","finished","resumed","duration"]);
    }
    
    
    /*
    public function create($request)
    {                
        return ProductionLog::create($request->all());
    }

    
    public function update($request)
    {       
      $model = ProductionLog::find($request->id);
      $model->fill($request->all())->save();
      return $model;

    }

    
    public function delete($request)
    {
        $model = ProductionLog::findOrFail($request->id);
        $model->delete();
    }    

    public function startProductionLog($request) {
        $model = ProductionLog::findOrFail($request->id);
        $model['idstatus'] = 2;
        $model->save();

    }

    public function finishProductionLog($request) {
        $model = ProductionLog::findOrFail($request->id);
        $model['idstatus'] = 4;
        $model->save();

    }

    public function pauseProductionLog($request) {
        $model = ProductionLog::findOrFail($request->id);
        $model['idstatus'] = 3;
        $model->save();

    }

    public function resumeProductionLog($request) {
        $model = ProductionLog::findOrFail($request->id);
        $model['idstatus'] = 2;
        $model->save();
    } */

    
    //
    // Retorna el log de una producción data
    // Si idproduction es null, retorna todo el log de las producciones actuales
    public function getProductionLog($request) 
    {        
        if ($request->idproduction != null)   {
            return ProductionLog::where('idproduction', $request->idproduction)->
            where('type',$request->type)->orderBy('updated_at','desc')
            ->get(["id", "idstop","name", "idproduction", "idequipment", "iduser", "idstatus", "updated_at"]);        
    }
        else {            

            if ($request->has('timezone')) { // viene de la app
                $today = Carbon::now($request->timezone)->startOfDay()->setTimezone('UTC');                  
            }
            else {
                if (Session::get('local_timezone') != null)
                    $today = Carbon::now(Session::get('local_timezone'))->startOfDay()->setTimezone('UTC');  
                else
                    $today = Carbon::now()->startOfDay()->setTimezone('UTC');  
            }

         
            return DB::table('wh_production as p')
                    ->join('wh_production_log as l', 'l.idproduction', '=', 'p.id')
                    ->where('productiondate', $today)
                    ->orderBy('l.updated_at','desc')
                    //->toSql();
                    ->get(["l.id", "idstop","l.name","p.id as idproduction", "p.idequipment", "iduser", "l.idstatus", "l.updated_at"]);                     
        }
    }



}