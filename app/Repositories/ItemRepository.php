<?php

namespace App\Repositories;

use App\Models\Item;
use App\Models\TicketType;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;


class ItemRepository
{
    private $config;

    public function __construct()
    {
        $this->config = new ConfigRepository;
    }

    public function getAll()
    {
        $items = DB::table('wh_item as i')
                   ->join('wh_ticket_type as tt', 'tt.id', '=', 'i.idtype')                                    
                   ->select('i.id as id', 'i.name as name', 'i.description', 'i.idtype', 'i.idteam', 'i.idchecklist', 'i.spots', 'i.isglitch', 'i.users','i.isprivate', 'i.idpriority', 'i.idprotocol', 'i.sla', 'i.enabled', 'i.code', 'tt.icon', 'tt.color', 'tt.hassla', 'tt.showingrid')
                   ->whereNull('i.deleted_at')
                   ->orderBy('i.name')
                   ->get();

        $items->map(function ($item){

            $item->users = array_map(array($this, 'formatUsers'), json_decode($item->users));
            return $item;
        });

        return $items;
    }

    public function getList($withTrashed = false)
    {
       return Item::where('enabled', true)->get(['id as value', 'name as text']);
    }

    public function getGlobal()
    {
        return DB::table('wh_item')->get(['id as value', 'name as text']);
    }

    /*public function getListCleaningTasks($request)
    {
        return Item::where('enabled', true)->get(['id as value', 'name as text']);
    }*/

    public function getCleaningItems()
    {
        return DB::table('wh_item as i')
            ->join('wh_ticket_type as tt', 'i.idtype', '=', 'tt.id')
            ->select('i.id as value', 'i.name as text')
            ->where('tt.iscleaningtask', '=', '1')
            ->where('i.enabled', '=', '1')
            ->where('i.isglitch','=','0')
            ->get();
    }

    public function create($request)
    {
        $request->merge(['users' => json_encode((array)$request->users)]);

        $item = Item::create($request->all());
    
        return response()->json(['success' => true, 'model' => $item]);
    }

    public function createOnFly($request)
    {
        $request['idteam'] = TicketType::find($request->idtype)->idteam;
        $request['users'] = json_encode([]);

        $item = Item::create($request->all());

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito', 'model' => $item->refresh()]);
    }

    private function pluckUsers($users)
    {
        $users = collect($users)->pluck('value')->toArray();
        $users = array_map('intval', $users);
        return $users;
    }

    public function update($request)
    {
        $request->merge(['users' => json_encode((array)$request->users)]);

        $item = Item::find($request->id);
        
        $item->fill($request->all())->save();

        return response()->json(['success' => true, 'model' => $item]);
    }

    public function delete($request)
    {
        $item = Item::findOrFail($request->id);

        $hasRelations = $this->config->checkRelations($item, ['tickets', 'checklistOptions']);

        if(!$hasRelations)
        {
            $item->delete();
            return response()->json(['success' => true, 'model' => $item]);
        }
        else
        {
            return response()->json(['success' => false, 'model' => $item, 'relations' => $hasRelations]);
        }
    }

    public function saveSpots($request)
    {

        $spots = is_null($request->spots) ? [] : array_map('intval', $request->spots);

        Item::where('id', $request->iditem)->update(['spots' => json_encode($spots)]);
        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    }

    public function getListApp($updated_at = null, $request = null)
    {
        $columns = ['i.id', 'i.name', 'i.idteam', 'i.idchecklist', 'i.code', 'tt.icon', 'tt.color', 'tt.hassla', 'tt.iscleaningtask', 'i.isglitch', 'i.isprivate', 'i.idprotocol', 'tt.showingrid', 'i.deleted_at', 'i.idpriority'];

        if(isset($request->version)) {
            if ($request->version > 411) {
                array_push($columns, 'i.spots');
            }
        }

        $items = DB::table('wh_item as i')
                    ->join('wh_ticket_type as tt', 'i.idtype', '=', 'tt.id')
                    ->select($columns)
                    ->when(!is_null($updated_at), function ($query) use ($updated_at){
                        return $query->where('i.updated_at', '>', $updated_at);
                    }, function ($query) {
                        return $query->whereNull('i.deleted_at');
                    })
                    ->get();

        $items->map(function ($item) {
            $item->icon = helper::formatIcon($item->icon);
        });

        return $items;
    }

    private function formatUsers($iduser)
    {
        $user = new \stdClass;
        $user->value = $iduser;
        return $user;
    }

    
    private function getItemsAPP($idtypes)
    {
        $items = DB::table('wh_item as i')
                    ->join('wh_ticket_type as tt', 'i.idtype', '=', 'tt.id')
                    ->select('i.id', 'i.name', 'i.idteam', 'i.idchecklist', 'tt.icon', 'tt.color')
                    ->whereIn('tt.id', $idtypes)
                    ->whereNull('i.deleted_at')
                    ->get();

        $items->map(function ($item) {
            $item->icon = helper::formatIcon($item->icon);
        });

        return $items;
    }
    
    public function getCleaningTypesAPP()
    {
        $settings = helper::getCleaningSettings();
        $cleaning_ticket_type = $settings->cleaning_ticket_type;
        $items = $this->getItemsAPP($cleaning_ticket_type);

        return $items;
    }
    
    public function getCleaningProductsAPP()
    {
        $settings = helper::getCleaningSettings();
        $cleaning_products = $settings->cleaning_products;
        $items = $this->getItemsAPP($cleaning_products);

        return $items;
    }
    
    public function searchCleaningProductsAPP($request)
    {
        $settings = helper::getCleaningSettings();
        $idtypes = $settings->cleaning_products;

        $items = DB::table('wh_item as i')
                    ->join('wh_ticket_type as tt', 'i.idtype', '=', 'tt.id')
                    ->select('i.id', 'i.name', 'i.idteam', 'i.idchecklist', 'tt.icon', 'tt.color')
                    ->whereIn('tt.id', $idtypes)
                    ->whereNull('i.deleted_at')
                    ->when(isset($request->name), function ($query) use($request) {
                        $query->Where('i.name', 'LIKE', "%$request->name%");
                    })
                    ->get();

        $items->map(function ($item) {
            $item->icon = helper::formatIcon($item->icon);
        });

        return $items;
    }

    public function restore($request)
    {
        $item = Item::withTrashed()->findOrFail($request->id);

        $item->restore();

        return response()->json(['success' => true, 'model' => $item]);
    }
}
