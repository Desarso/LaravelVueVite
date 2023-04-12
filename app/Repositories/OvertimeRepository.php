<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;

class OvertimeRepository
{
    public function getList()
    {
        return DB::table('wh_overtime')->get(['id as value', 'name as text']);
    }
}