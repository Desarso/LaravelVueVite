<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\TicketStatus;

class TicketStatusRepository
{
    public function getAll()
    {
        return DB::table('wh_ticket_status')->get(['id', 'name', 'color']);
    }

    public function getList()
    {
        return DB::table('wh_ticket_status')->get(['id as value', 'name as text', 'color']);
    }

    public function getListApp()
    {

        return DB::table('wh_ticket_status')
                ->select('id', 'name', 'color', 'nextstatus')
                ->get();
    }
}