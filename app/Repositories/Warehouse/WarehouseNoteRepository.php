<?php

namespace App\Repositories\Warehouse;

use Illuminate\Support\Facades\DB;
use App\Models\Warehouse\WarehouseNote;
use Illuminate\Support\Facades\Auth;
use Session;
use Carbon\Carbon;

class WarehouseNoteRepository
{
    public function getNotes($request)
    {
        $notes = DB::table('wh_warehouse_note as wn')
                   ->join('wh_user as r', 'r.id', '=', 'wn.created_by')
                   ->where('wn.idwarehouse', $request->id)
                   ->select('wn.id', 'wn.note', 'wn.created_at', 'wn.created_by', DB::raw('CONCAT(r.firstname, " ", r.lastname) AS fullname'), 'r.urlpicture')
                   ->orderBy('wn.created_at')
                   ->get();

        $notes->each(function ($note, $key) {
            $note->created_at = Carbon::parse($note->created_at)->setTimezone(Session::get('local_timezone'));
        });

        return view('pages.warehouse.notes', ["notes" => $notes]);
    }

    public function create($request)
    {
        $request['created_by'] = Auth::id();

        $note = WarehouseNote::create($request->all());

        return response()->json(['success' => true]);
    }
}