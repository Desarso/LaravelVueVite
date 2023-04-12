<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\AssetLoan;
use App\Models\AssetLoanNote;
use App\Models\User;
use App\Models\Asset;
use App\Helpers\Helper;
use App\Enums\AssetLoanStatus;
use Carbon\Carbon;

class AssetLoanRepository
{
    public function getData($request)
    {
        $data = AssetLoan::withCount('notes')
                         ->when(!is_null($request->search), function ($query) use ($request) {
                            return $query->where('id', 'LIKE', '%'. $request->search .'%');  
                         })  
                         ->when(!is_null($request->status), function ($query) use ($request) {
                            return $query->where('status', $request->status);
                         })
                         ->when(!is_null($request->idasset), function ($query) use ($request) {
                            return $query->where('idasset', $request->idasset);
                         })
                         ->when(!is_null($request->iduser), function ($query) use ($request) {
                            return $query->where('iduser', $request->iduser);
                         })
                         ->when($request->overdue == "true", function ($query) use ($request) {
                            return $query->where('status', 'OPEN')
                                         ->where('duedate', "<", Carbon::now());
                         })
                         
                         ->orderBy('status')
                         ->orderBy('created_at', 'desc');

        $total = $data->count('id');

        return array("total" => $total, "data" => $data->skip($request->skip)->take($request->take)->get());
    }

    public function create($request)
    {
        if($this->checkIfIsLoaned($request->idasset))
        {
            return response()->json(['success' => false, 'message' => 'El activo no está disponible']);
        }

        $request['create_by'] = Auth::id();

        $assetLoan = AssetLoan::create($request->all());

        Asset::whereId($assetLoan->idasset)->update(["isloaned" => true]);

        return response()->json(['success' => true, 'model' => $assetLoan]);
    }

    public function update($request)
    {
        $assetLoan = AssetLoan::find($request->id);

        $assetLoan->fill($request->all())->save();

        return response()->json(['success' => true, 'model' => $assetLoan]);
    }

    public function delete($request)
    {
        $assetLoan = AssetLoan::findOrFail($request->id);
        $assetLoan->delete();

        Asset::whereId($assetLoan->idasset)->update(["isloaned" => false]);

        return response()->json(['success' => true, 'model' => $assetLoan]);
    }

    public function changeStatus($request)
    {
        $request["status"] = "CLOSE";
        $request['returned_date'] = Carbon::now();

        $assetLoan = AssetLoan::find($request->id);

        $assetLoan->fill($request->all())->save();

        Asset::whereId($assetLoan->idasset)->update(["isloaned" => false]);

        return response()->json(['success' => true, 'model' => $assetLoan]);
    }

    public function checkIfIsLoaned($idasset)
    {
        $assetLoan = AssetLoan::where("idasset", $idasset)->where("status", "OPEN")->first();

        return (is_null($assetLoan) ? false : true);
    }

    public function get($request)
    {
        return AssetLoan::with('asset:id,name,code,photo')
                        ->with('createBy:id,firstname,lastname')
                        ->with('user:id,firstname,lastname')
                        ->with('userReturned:id,firstname,lastname')
                        ->find($request->id);
    }

    public function getLastChange()
    {
        $assetLoan = AssetLoan::orderBy('updated_at', 'desc')->first();

        return (is_null($assetLoan) ? "null" : $assetLoan->updated_at);
    }

    public function getListLoanAPP($request)
    {
        $hasRangeDate = $request->hasRangeDate === "false" ? false : true;
        $user = User::find($request->iduser);

        return AssetLoan::with('asset:id,name,code')
                ->with('user:id,firstname,lastname,urlpicture')
                ->skip($request->skip)->take($request->take)
                ->when($request->hasfilter == 'true', function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        return $this->filterListLoanAPP($q, $request);
                    });
                 })
                ->when($user->isadmin == 0, function ($query) use ($request) {
                    $query->where('iduser', $request->iduser);
                 })
                 ->when($hasRangeDate, function ($query) use ($request) {
                    
                    $start = Carbon::parse($request->start)->startOfDay();
                    $end   = Carbon::parse($request->end)->endOfDay();
                    
                    $query->whereBetween('created_at', [$start, $end]);
                 })
                ->orderBy('status')
                ->orderBy('created_at', 'DESC')
                ->get(["id", "status", "idasset", "iduser", "duedate", "created_at"]);
    }

    private function filterListLoanAPP($query, $request)
    {
        $filters = json_decode($request->filters);

        foreach($filters as $filter)
        {
            switch ($filter->field) {
                case 'asset':
                    $query->whereHas('asset', function ($query) use ($filter) {
                        $query->where('idasset', $filter->value);
                    });
                    break;
                    
                case 'user':
                    $query->whereHas('user', function ($query) use ($filter) {
                        $query->where('iduser', $filter->value);
                    });
                    break;

                case 'status':
                    $query->where('status', $filter->value);
                    break;
            }
        }

        return $query;
    } 

    public function createAssetLoanAPP($request)
    {   
        $success = false;
        $message = '';

        if ($request->has('signature')) {
            $url = helper::UploadImageApp([$request->signature]);
            $request['signature'] = $url;
        }

        $assets = json_decode($request->assets);
        $request->except(['assets']);

        foreach ($assets as $idasset) {

            $newAsset = $request->all();
            $newAsset['idasset'] = $idasset;

            // Asset::whereId($assetLoan->idasset)->update(["isloaned" => true]);
            $asset = Asset::find($idasset);

            if ($asset->isloaned == true) {
                $message = 'Activo: #'.$asset->code.', No disponible.';
                $success = false;
            } else {
                $assetLoan = AssetLoan::create($newAsset);

                if ($assetLoan) {
                    Asset::whereId($assetLoan->idasset)->update(["isloaned" => true]);
                    $success = true;
                }
            }
        }

        return response()->json([
            'success' => $success,
            'message' => $message
        ]);
    }

    public function getAssetLoanDetailsAPP($request) {
        
        return AssetLoan::where('id', $request->idloan)
                ->with('asset:id,name,code,photo')
                ->with('user:id,firstname,lastname,urlpicture')
                ->with('userReturned:id,firstname,lastname,urlpicture')
                ->first();
    }

    public function closeAssetLoanAPP($request)
    {
        $request["status"] = "CLOSE";
        $request['returned_date'] = Carbon::today();

        $assetLoan = AssetLoan::find($request->id);

        $assetLoan->fill($request->all())->save();
        Asset::whereId($assetLoan->idasset)->update(["isloaned" => false]);

        return response()->json(['success' => true, 'model' => $assetLoan]);
    }

    public function getAssetLoanNotesAPP($request)
    {
        return DB::table('wh_asset_loan_note as ln')
                   ->join('wh_user as r', 'r.id', '=', 'ln.created_by')
                   ->where('ln.idassetloan', $request->idloan)
                   ->select('ln.id AS idserver','ln.note', 'ln.type', 'ln.created_at', DB::raw('CONCAT(r.firstname, " ", r.lastname) AS createdName'), 'r.urlpicture', 'ln.created_by')
                   ->get();
    }

    public function createAssetLoanNotesAPP($request)
    {

        if ($request->type == 'IMG') {
            $url = helper::UploadImageApp([$request->note]);
            $request['note'] = $url;
        }

        $note = AssetLoanNote::create($request->all());
        return response()->json(['success' => true, 'data' => $note]);
    }

    public function deleteAssetLoanNotesAPP($request)
    {
        $note = AssetLoanNote::where('idassetloan' , '=', $request->idloan)
                            ->where('id' , '=', $request->idnote)
                            ->first();
        
        if ($note) $note->delete();

        return response()->json(['success' => true]);
    }

    public function deleteAssetLoanAPP($request)
    {
        $loan = AssetLoan::find($request->idloan);
        
        if ($loan) {
            $deleted = $loan->delete();

            if ($deleted) {
                Asset::whereId($loan->idasset)->update(["isloaned" => false]);
            }
        }

        return response()->json(['success' => true]);
    }
}
