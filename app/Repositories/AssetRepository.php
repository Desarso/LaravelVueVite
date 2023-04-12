<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Asset;
use App\Models\Ticket;
use App\Helpers\Helper;
use App\Enums\TicketStatus;
use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Image;

class AssetRepository
{
    protected $checklistOptionRepository;
    protected $configRepository;

    public function __construct()
    {
        $this->checklistOptionRepository = new ChecklistOptionRepository;
        $this->configRepository = new ConfigRepository;
    }

    public function getAll()
    {
        return Asset::get();
    }

    public function getList()
    {
        return Asset::get(['id as value', 'name as text', 'code', 'photo', 'isloaned']);
    }


    public function getAssetsData()
    {

        $data = DB::table('wh_asset_status as cs')
            ->leftJoin('wh_asset as cp', function ($join) {
                $join->on('cp.idstatus', '=', 'cs.id');
            })
            ->select('cs.id', 'cs.color', 'cs.name', DB::raw('count(cp.id) as asset_count'))
            ->groupBy('cs.id')
            ->orderBy('cs.name', 'Desc')
            ->get();

        return $data;
    }

    public function getAssetInfo($request)
    {
        return Asset::with([
            "status" => function ($q) {
                $q->select('id', 'name', 'color', 'icon');
            },
            "category" => function ($q) {
                $q->select('id', 'name');
            },
            "spot" => function ($q) {
                $q->select('id', 'name');
            }
        ])->find($request->id);
    }

    public function saveAssetQRCode($request)
    {
        $model = Asset::find($request->id);
        $model->qrcode = $request->qrcode;
        $model->save();
    }

    public function create($request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:wh_asset',
        ]);

        if($validator->passes())
        {
            $asset = Asset::create($request->all());

            if($request->has('file'))
            {
                $this->uploadImage($request, $asset);
            }
    
            return response()->json(['success' => true, 'model' => $asset]);
        }

        return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
    }
   
    public function uploadImage($request, $asset)
    {
        $client = env('DO_SPACES_HOTEL', 'whagons');
        $path   = env('DO_SPACES_ROUTE', 'https://dingdonecdn.nyc3.digitaloceanspaces.com/');

        $route = Storage::disk('spaces')->putFile(($client . '/assets'), $request->file('file'), 'public'); 

        $asset->photo = ($path . $route);
        $asset->save();
    }

    public function removeImage($asset)
    {
        $url = explode('/', $asset->photo);

        $deleted = Storage::disk('spaces')->delete($url[3] . '/' . $url[4] . '/' . $url[5]);

        $asset->photo = null;
        $asset->save();
    }

    public function update($request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:wh_asset,code,' . $request->id,
        ]);

        if($validator->passes())
        {
            $asset = Asset::find($request->id);

            if(!is_null($asset->photo) && $request->hasPreview == "false") $this->removeImage($asset);

            $asset->fill($request->all())->save();

            if($request->has('file')) $this->uploadImage($request, $asset);

            return response()->json(['success' => true, 'model' => $asset]);
        }
        
        return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
    }

    public function delete($request)
    {
        $asset = Asset::findOrFail($request->id);

        $hasRelations = $this->configRepository->checkRelations($asset, ['loans']);

        if(!$hasRelations)
        {
            $asset->delete();
            return response()->json(['success' => true, 'model' => $asset]);
        }
        else
        {
            return response()->json(['success' => false, 'model' => $asset, 'relations' => $hasRelations]);
        }
    }

    public function downloadQR($request)
    {
        $asset = Asset::find($request->id);

        return PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
                  ->loadView('/pages.config.assets.asset-qr-pdf', ["asset" => $asset])
                  ->stream('test.pdf');   
    }

    public function createTicket($request)
    {
        $item = DB::table('wh_item')->where('id', $request->iditem)->first(['name', 'idteam', 'idchecklist']);

        $request['uuid']       = uniqid();
        $request['name']       = $item->name;
        $request['idteam']     = $item->idteam;
        $request['created_by'] = Auth::id();
        $request['updated_by'] = Auth::id();
        $users                 = (array)$request->iduser;

        $ticket = Ticket::create($request->all());

        $ticket->users()->wherePivot('copy', 0)->attach($users);

        if (!is_null($item->idchecklist)) {
            $checklist_copy = $this->checklistOptionRepository->getChecklistCopy($item->idchecklist, $ticket->id);
            $ticket->checklists()->create($checklist_copy);
        }

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    }


    public function getAssetTasks($request)
    {
        $items = DB::table('wh_ticket as t')
            ->select('t.id', 't.code', 't.name', 's.name as spot', 'tt.icon', 'tt.color', 't.idstatus', 't.created_by', 't.created_at')
            ->join('wh_item as i', 't.iditem', '=', 'i.id')
            ->join('wh_spot as s', 't.idspot', '=', 's.id')
            ->join('wh_ticket_type as tt', 'i.idtype', '=', 'tt.id')
            ->where('t.idstatus', "!=", TicketStatus::Finished)
            ->where('t.idasset', $request->idasset)
            ->whereNull('t.deleted_at')
            ->orderBy('t.id', 'desc')
            ->get();

        return $items;
    }

    public function getListAPP($request)
    {
        $items = Asset::where('enabled', true)
            ->with('category:id,name')
            ->with('status:id,name,color')
            ->with('spot:id,name')
            ->get(["id", "name", "description", "code", "brand", "model", "icon", "color", "idcategory", "idstatus", "idspot"]);

        $items->map(function ($item) {
            if ($item->icon != null) {
                $item->icon = helper::formatIcon($item->icon);
            }
        });

        return $items;
    }

    public function getAssetInfoAPP($request)
    {
        $asset = Asset::select("id", "name", "description", "code", "brand", "model", "icon", "color", "idcategory", "idstatus", "idspot")
            ->with('category:id,name')
            ->with('status:id,name,color')
            ->with('spot:id,name')
            ->where('enabled', true)
            ->find($request->id);

        if ($asset->icon != null) {
            $asset->icon = helper::formatIcon($asset->icon);
        }

        return $asset;
    }

    public function searchAssetsAPP($request)
    {
        return Asset::select('id', 'name', 'code', 'isloaned', 'photo')
            ->when(isset($request->name), function ($query) use ($request) {
                $query->Where('name', 'LIKE', "%$request->name%")
                      ->orWhere('code', 'LIKE', "%$request->name%");
            })
            ->get();
    }

    public function getTicketAssetAPP($request)
    {
        $items = DB::table('wh_ticket as t')
            ->select('t.id', 't.code', 't.name', 's.name as spot', 'tt.icon', 'tt.color', 't.idstatus', 't.created_by', 't.created_at')
            ->join('wh_item as i', 't.iditem', '=', 'i.id')
            ->join('wh_spot as s', 't.idspot', '=', 's.id')
            ->join('wh_ticket_type as tt', 'i.idtype', '=', 'tt.id')
            ->where('t.idstatus', "!=", TicketStatus::Finished)
            ->where('t.idasset', $request->idasset)
            ->whereNull('t.deleted_at')
            ->orderBy('t.id', 'desc')
            ->get();

        $items->map(function ($item) {
            $item->icon = helper::formatIcon($item->icon);
        });

        return $items;
    }
}
