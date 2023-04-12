<?php

namespace App\Repositories\Production;

use App\Enums\Production\EquipmentStatus;
use App\Enums\Production\ProductionStatus;
use App\Models\Production\Equipment;
use App\Models\Production\Product;
use App\Models\Production\Production;
use App\Models\Production\ProductionLog;
use App\Models\Production\ProductionSchedule;
use App\Models\Production\ProductionFormula;
use App\Models\Production\ProductionBreak;
use App\Models\Production\ProductionInput;
use App\Models\Production\ProductionStop;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;

use App\Events\ProductionLogCreated;

class ProductionRepository
{
    public function getAll($request)
    {
        $production = Production::select(['id','idequipment',
        'idproduct','idschedule', 'idpresentation', 'iddestination',
        'idstatus', 'productiongoal', 'productionorder',
        'lot', 'idoperator', 'productiondate'])
        
       ->when($request->has('filter'), function ($query) use ($request) {
            //Filtros del buscador
            $query->where(function ($q) use ($request) {
                return $this->applyFilters($q, $request);
            });
        })
        ->when($request->has('sort'), function ($query) use($request){
            return $this->sort($query, $request->sort);
         }, function ($query) {
            return $query->orderBy('id', 'desc');
         });
        
        $total = $production->count('id');
        $productions = $production->skip($request->skip)->take($request->take)->latest()->get();
        
        return array('total' => $total, "data" => $productions);
        
    }
    
    
    
    // Ocupamos pasar al timezone..
    // Con BE podemos ->setTimezone(Session::get('local_timezone'));
    // TODO: Como hacemos si se está llamando desde la APP ???
    public function getCurrentProduction($request)
    {
        // TODO: ignorar enabled = false
        //  UTC -> tIMEZONE -> QUITARLES LAS HORAS -> VOLVER AL UTC

        ////// TODO: descomentar ////        
        

        if ($request->has('timezone')) // viene de la app
            $today = Carbon::now($request->timezone)->startOfDay()->setTimezone('UTC');  
        else
            if (Session::get('local_timezone') != null)
                $today = Carbon::now(Session::get('local_timezone'))->startOfDay()->setTimezone('UTC');  
            else
                $today = Carbon::now()->startOfDay()->setTimezone('UTC');  

        //$today = Carbon::now()->startOfDay()->setTimezone('UTC');  
      
        
        return Equipment::with(["status" => function($q) {
                $q->select('id','name','color','icon');
            },
            "productions.product" => function($q) {
                $q->select('id','name', 'idequipmenttype','idproductcategory','iddestination');
            }, 
            "productions.operator" => function($q) {
                $q->select('id','firstname', 'lastname','urlpicture');
            }, 
            "productions.status" => function($q) {
                $q->select('id','name','color','icon');
            },
            "productions.presentation" => function($q) {
                $q->select('id','name','units','idequipmenttype','isendproduct');
            }, 
            "productions.destination" => function($q) {
                $q->select('id','name');   
            },
            "productions.schedule" => function($q) {
                $q->select('id','name', 'description');   
            },
            "productions" => function($q) use($today) {                
                // Debemos considerar que una producción no se haya terminado y venga arrastrada
                // del día anterior....
                $q->where('productiondate', $today);
                
            }
        ])
        ->orderBy('id')
        ->get();
    }
 


    public function initializeProduction($request)
    {
         // TODO: ignorar enabled = false
         $today = Carbon::now()->setTimezone(Session::get('local_timezone'))->toDateString();        
        // Apagar todas las máquinas
        Equipment::query()->update(['idstatus' =>1, 'idproduction' => null]);
        Production::where('productiondate', $today)->update(
            [
                'idstatus' => 1, 
                'idoperator' => null,
                'productionstarted' => null,
                'productionfinished' => null,
                'initialcount'  => 0,
                'finalcount' => 0,
                'totalproduced' => 0
        ]);

    }
    
    public function getList()
    {
        return Production::get(['id as value', 'name as text']);
    }
    

    public function fixProductiondate($request)
    {
        // Comming from app
        if ($request->has('timezone'))    
            return Carbon::parse($request->productiondate, $request->timezone)->startOfDay()->setTimezone('UTC');
        else
            return Carbon::parse($request->productiondate, Session::get('local_timezone'))->startOfDay()->setTimezone('UTC');
    }

    public function create($request){

        $product = Product::find($request->idproduct);
        $request['productiondate'] = $this->fixProductiondate($request);
        // TODO: la presentación debe ser un campo que se toma de producto como el destino.
        $request['idpresentation'] = $product->idpresentation;
        $request['iddestination'] = $product->iddestination;
        
        $request['productiongoal'] = $this->calculateProjection($request);

       return Production::create($request->all());
        
    }

    public function createFromApp($request) 
    {
        
        return $this->create($request);
    }
    
    public function update($request)
    {
        // Si se edita la producción...debería recalcular la proyección
        // solo si el estado es diferente a finalizado...
        
        $request["productiondate"] = $this->fixProductiondate($request);
        $model = Production::find($request->id);
        if ($request->idstatus != 4)        
            $request['productiongoal'] = $this->calculateProjection($request);

        $model->fill($request->all())->save();
        return $model;
    }
    
    public function delete($request)
    {
        $model = Production::findOrFail($request->id);
        $model->delete();
     }
    
     ////////////////////////////////////////////////////////////////////////////////////////
     // PROJECTION CALCULATIONS

     private function calculateProjection($request)
     {        
        $Mn = $this->getMn($request->idschedule);
        $velocity = $this->getVelocity($request->idequipment);
        // Cantidad a producir Bruta (sin considerar duración de insumos)
        $Cb = $Mn * $velocity;
         
        $Ti = $this->estimateInputDuration($Cb,$request);
        
       
        return ($Mn - $Ti) * $velocity;
     }

   
    private function estimateInputDuration($Cb, $request)
    {
        $duration = 0;
        // debo Obtener la fórmula del producto primero
        $product = Product::findOrFail($request->idproduct);
        $formula = ProductionFormula::findOrFail($product->idformula);
        $inputs = json_decode($formula->inputs);        
        
        for ($i = 0; $i < count($inputs); $i++) {
            $duration += $this->getInputDuration($inputs[$i], $Cb);
        }
        return $duration/60;
    }

      // Formula para calcular el tiempo que cada insumo resta al tiempo de la producción
     // (Cel[Cb / pack_size ] * pack_placing_duration) + buffer
    private function getInputDuration($input, $Cb)
    {        
        $i = ProductionInput::findOrFail($input);        
        return ((ceil( $Cb / $i->pack_size) + $i->buffer) * $i->pack_placing_duration) ;
    }

    private function getVelocity($idequipment)
    {
        $machine = Equipment::findOrFail($idequipment);
        if ($machine != null)
            return $machine->velocity;
        return 0;

    }

    // Minutos Netos = Duración del horario - Sum(Interrupciones) 
     private function getMn($idschedule) 
     {
        $schedule = ProductionSchedule::findOrFail($idschedule);
        // Minutos Brutos (Duración del Horario) * 60
        $Mb = $schedule->duration * 60;      
        // Obtener duración de las interrupciones
        $Int = $this->getTi($schedule->breaks);              
        return $Mb - $Int;        
     }


     // Calcular la duración de insumos dado un horario
     private function getTi($breaks)
     {
        $breaks = json_decode($breaks);
        $total = 0;
        for ($i = 0; $i < count($breaks); $i++) {
            $total += $this->getBreakDuration($breaks[$i]);
        }
        return $total;
     }

     private function getBreakDuration($idbreak)
     {     
        $dow = Carbon::now(Session::get('local_timezone'))->dayOfWeek;                    
        $break = ProductionBreak::find($idbreak);
         if ($break == null) return 0;
         if ($break->enabled == false) return 0;
        // Ver si el break aplica solo ciertos días...        
         if ($break->dow != null && $break->dow != '[]') {
            $dows = json_decode($break->dow);                       
           
            if (in_array([$dow], $dows))
                return $break->duration;
            else
                return 0;
         }
         return $break->duration;
     }


    ////////////////////////////////////////////////////////////////////////////////////////
    public function startProduction($request)
    {
        //Start Production
        $production = Production::find($request->idproduction);
        $production->idstatus = ProductionStatus::Progress;
        $production->idoperator = $request->idoperator;
        $production->initialcount = $request->initialcount;
        $production->productionstarted = Carbon::now();
        $production->save();
        
        // Start Equipment
        $equipment = Equipment::find($production->idequipment);
        $equipment->idproduction = $request->idproduction;
        $equipment->idstatus = EquipmentStatus::Working;
        $equipment->save();
    }
    
    public function finishProduction($request)
    {
        //Finish Production
        $production = Production::find($request->idproduction);
        $production->idstatus = ProductionStatus::Finished;
        $production->finalcount = $request->finalcount;
        $production->productionfinished = Carbon::now();
        $production->save();
        
        // Off Equipment
        $equipment = Equipment::find($production->idequipment);
        $equipment->idstatus = EquipmentStatus::Off;
        $equipment->save();
    }
    
    public function addProductionDetail($request)
    {
        $model = $request->all();
        $product = Product::find($model['idproduct']);
        $model['idpresentation'] = $product->presentations->first()->id;
        $model['iddestination'] = $product->iddestination;
        return Production::create($model);
    }

   


    
    public function getLast()
    {
        $count = DB::table('wh_production')->count();
        if ($count > 0)
            return DB::table('wh_production')->orderBy('updated_at', 'desc')->first()->updated_at;
        else
            return null;
    }
    
    
    public function updateEquipmentProduction($request)
    {
        // Update idproduction in Equipment
        $eq = Equipment::find($request->idequipment);
        $eq->idproduction = $request->idproduction;
        $eq->save();
        // if idproduction is not null, update idoperator and schedule of that production
        if ($request->idproduction != null) {
            $prod = Production::find($request->idproduction);
            $prod->idoperator = $request->idoperator;            
            $prod->idschedule = $request->idschedule;
            $prod->save();
        }
    }
    private function applyFilters($model, $request)
    {
        $logic = $request->filter['logic'];
        $filters = $request->filter['filters'];
        
        foreach($filters as $filter)
        {
            switch ($filter['operator'])
            {
                case "eq":
                    $this->getFilterEq($model, $filter, $logic);
                break;
                
                case "contains":
                    $this->getFilterContains($model, $filter, $logic);
                break;
            }
        }
        
        return $model;
    }
    
    private function getFilterContains($model, $filter, $logic)
    {
        switch ($filter['field'])
        {
            case "note":
                
                $model->when($logic == "and", function ($query) use ($filter) {
                    return $query->whereHas('notes', function ($query) use ($filter) {
                        $query->where('note', 'like', '%' . $filter['value'] . '%');
                    });
                }, function ($query) use ($filter) {
                    return $query->orWhereHas('notes', function ($query) use ($filter) {
                        $query->orWhere('note', 'like', '%' . $filter['value'] . '%');
                    });
                });
                
            break;
            
            default:
            
            $model->when($logic == "and", function ($query) use ($filter) {
                return $query->where($filter['field'], 'like', '%' . $filter['value'] . '%');
            }, function ($query) use ($filter) {
                return $query->orWhere($filter['field'], 'like', '%' . $filter['value'] . '%');
            });
        }
        
        return $model;
    }
    
    private function getFilterEq($model, $filter, $logic)
    {
        switch ($filter['field'])
        {
            case "iduser":
                
                $model->when($logic == "and", function ($query) use ($filter) {
                    return $query->whereHas('users', function ($query) use ($filter) {
                        $query->where('iduser', $filter['value']);
                    });
                }, function ($query) use ($filter) {
                    return $query->orWhereHas('users', function ($query) use ($filter) {
                        $query->where('iduser', $filter['value']);
                    });
                });
                
            break;
            
            case "idtag":
                
                $model->when($logic == "and", function ($query) use ($filter) {
                    return $query->whereHas('tags', function ($query) use ($filter) {
                        $query->where('idtag', $filter['value']);
                    });
                }, function ($query) use ($filter) {
                    return $query->orWhereHas('tags', function ($query) use ($filter) {
                        $query->where('idtag', $filter['value']);
                    });
                });
                
            break;
            
            case "idtype":
                
                $model->when($logic == "and", function ($query) use ($filter) {
                    return $query->whereHas('item', function ($query) use ($filter) {
                        $query->where('idtype', $filter['value']);
                    });
                }, function ($query) use ($filter) {
                    return $query->orWhereHas('item', function ($query) use ($filter) {
                        $query->where('idtype', $filter['value']);
                    });
                });
                
            break;
            
            case "idbranch":
                
                $spots = $this->spotRepository->getChildren($filter['value']);
                
                $model->when($logic == "and", function ($query) use ($filter, $spots) {
                    return $query->whereIn('idspot', $spots);
                }, function ($query) use ($filter, $spots) {
                    return $query->orWhereIn('idspot', $spots);
                });
                
            break;
            
            default:
            
            $model->when($logic == "and", function ($query) use ($filter) {
                return $query->where($filter['field'], $filter['value']);
            }, function ($query) use ($filter) {
                return $query->orWhere($filter['field'], $filter['value']);
            });
        }
        
        return $model;
    }
    
    private function sort($query, $sorts)
    {
        foreach($sorts as $sort) {
            $query->orderBy($sort["field"], $sort["dir"]);
        }
        return $query;
    }
    

    ////////// STOPS ///////////////////////////

    public function reportStop($request) 
    {   
        
        $stop = ProductionStop::findOrFail($request->idstop);
        $request['idteam'] = $stop->idteam;
        
        // Creamos el log de la parada
        $log = ProductionLog::create($request->all());
        
        event(new ProductionLogCreated($log));

        // Detenemos la máquina
        $eq = Equipment::find($request->idequipment);
        $eq->idstatus = 3; // Detenida
        $eq->save();
        // Pausamos la producción
        $pr = Production::find($request->idproduction);
        $pr->idstatus = 3; // Pausada
        return $pr->save();
    }

    public function updateReportedStop($request) 
    { 
        $pl = ProductionLog::find($request->id);
        $pl->fill($request->all())->save();
        return $pl;
    }

    public function discardStop($request)
    {
        $pl = ProductionLog::findOrFail($request->id);
        $pl->iduser = $request->iduser;
        $pl->delete();
        // ponemos máquina a trabajar
        $eq = Equipment::find($request->idequipment);
        $eq->idstatus = 2; // Trabajando
        $eq->save();
        // Reanudamos la producción
        $pr = Production::find($request->idproduction);
        $pr->idstatus = 2; // En Progreso
        return $pr->save();
    }



    public function finishStop($request) {
    
        
        $pl = ProductionLog::find($request->id);
        $pl->idstatus = 4;
        $pl->iduser = $request->iduser;
        $pl->finished =  Carbon::now();  
        $pl->duration = $pl->created_at->diffInSeconds(Carbon::now());

        $pl->save();
        // ponemos máquina a trabajar
        $eq = Equipment::find($request->idequipment);
        $eq->idstatus = 2; // Trabajando
        $eq->save();
        // Reanudamos la producción
        $pr = Production::find($request->idproduction);
        $pr->idstatus = 2; // En Progreso
        return $pr->save();

    }
    
}