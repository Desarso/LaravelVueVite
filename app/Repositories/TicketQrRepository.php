<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\TicketQr;

class TicketQrRepository
{

    public function getAll($request)
    {
        return TicketQr::get(['id', 'iditem', 'idspot']);
    }

    public function create($request)
    {
        $ticketQr = TicketQr::create($request->all());
        return $ticketQr;
    }

    public function delete($request)
    {
        $model = TicketQr::findOrFail($request->id);
        $model->delete();

        return response()->json(['success' => true]);
    }

}