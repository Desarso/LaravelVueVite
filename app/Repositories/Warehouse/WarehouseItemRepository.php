<?php

namespace App\Repositories\Warehouse;

use App\Models\Warehouse\WarehouseItem;
use Illuminate\Support\Facades\DB;
use Session;
use Carbon\Carbon;

class WarehouseItemRepository
{
    public function getAll($request)
    {
        $warehouseItem = DB::table('wh_warehouse_item as wi')->whereNull('deleted_at')->select('wi.id', 'wi.name', 'wi.code', 'wi.description', 'wi.idcategory', 'wi.enabled as enabled')

        /*
        $warehouseItem = WarehouseItem::select(['id', 'name', 'code', 'description', 'enabled'])
        */

        ->when($request->has('filter'), function ($query) use ($request) {
            //Filtros del buscador
            $query->where(function ($q) use ($request) {
                return $this->applyFilters($q, $request);
            });
        })
        ->when($request->has('sort'), function ($query) use($request){
            return $this->sort($query, $request->sort);
        }, function ($query) {
            return $query->orderBy('wi.id', 'desc');
        });
        
        $total = $warehouseItem->count('wi.id');
        $warehouses = $warehouseItem->skip($request->skip)->take($request->take)->latest()->get();
        
        return array('total' => $total, "data" => $warehouses);  
    }

    public function getValueMapper($request)
    {
        $row = 0;
        if($request->values[0] != null){
            $itemId = $request->values[0];
            $results = DB::select( 
            DB::raw("
                SELECT id,name, row FROM 
                (SELECT t.*, @row := @row+1 AS row FROM wh_warehouse_item t, (SELECT @row:=0) r) as temporalTable
            WHERE id = :itemid"), 
            array('itemid' => $itemId)
        );
        $row = (int)$results[0]->row  - 1;
        }
        
        return $row;
    }

    public function getAllWarehouseItems($request)
    {
        $models = DB::table('wh_warehouse_item')
                    ->when(isset($request->filter['filters']), function ($query) use($request){
                        $filter = $request->filter['filters'][0]['value'];
                        return $query->where("name", 'like', '%'. $filter .'%');
                    });

       $total = $models->count();

       if($request->has('skip'))
       {
            $rows = $models->take($request->take)->skip($request->skip)->get();
       }
       else
       {
            $rows = $models->take(80)->get();
       }

       return ["total" => $total, "data" => $rows];
    }

    public function getList()
    {
        return WarehouseItem::where('enabled', true)->get(['id as value', 'name as text']);
    }

    public function getLast()
    {
        return DB::table('wh_warehouseItem')->orderBy('updated_at', 'desc')->first()->updated_at;
    }

    public function create($request)
    {
        return WarehouseItem::create($request->all());
    }

    
    public function update($request)
    {
        $model = WarehouseItem::find($request->id);
        $model->fill($request->all())->save();
        return $model;
    }    

    public function delete($request)
    {
        $model = WarehouseItem::findOrFail($request->id);
        $model->delete();
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

    /********** APP`s functions **************/
    
    public function searchItemsWarehouseAPP($request)
    {
        return WarehouseItem::select('id', 'name')
                    ->when(isset($request->name), function ($query) use($request) {
                        $query->Where('name', 'LIKE', "%$request->name%");
                    })
                    ->get();
    }
}//Final