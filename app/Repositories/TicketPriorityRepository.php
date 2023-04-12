<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\TicketPriority;

class TicketPriorityRepository
{
    public function getAll()
    {
        return DB::table('wh_ticket_priority')->get();
    }

    public function getList()
    {
        return DB::table('wh_ticket_priority')->get(['id as value', 'name as text', 'isurgent', 'color', 'sla', 'options']);
    }

    public function getListApp($updated_at = null)
    {
        $items = DB::table('wh_ticket_priority')
            ->select('id', 'name', 'color', 'sla', 'options')
            ->when(!is_null($updated_at), function ($query) use ($updated_at) {
                return $query->where('updated_at', '>', $updated_at);
            })
            ->get();

        return $items;
    }

    public function update($request)
    {
        $ticketPriority = TicketPriority::find($request->id);

        $ticketPriority->fill($request->all())->save();

        return $ticketPriority;
    }
}
