<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Asset;
use App\Models\AssetLoanNote;
use App\Helpers\Helper;
use App\Enums\AssetLoanStatus;
use Carbon\Carbon;

class AssetLoanNoteRepository
{
    public function getNotes($request)
    {
        if(is_null($request->idassetloan)) return [];

        return AssetLoanNote::where("idassetloan", $request->idassetloan)->get();
    }

    public function create($request)
    {
        $request['created_by'] = Auth::id();
        $request['type'] = 1;

        $note = AssetLoanNote::create($request->all());

        return response()->json(['success' => true, 'data' => $note]);
    }

    public function delete($request)
    {
        $note = AssetLoanNote::find($request->id);
        $note->delete();

        return response()->json(['success' => true, 'data' => $note]);
    }
}
