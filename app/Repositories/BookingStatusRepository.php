<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\BookingStatus;

class BookingStatusRepository
{
    public function getAll()
    {
        return DB::table('wh_booking_status')->get(['id', 'name', 'color','icon']);
    }

    public function getList()
    {
        return DB::table('wh_booking_status')->get(['id as value', 'name as text', 'color', 'icon']);
    }
}