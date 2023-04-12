<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Priority;

class PriorityRepository
{
    public function getAll()
    {
        return DB::table('wh_priority')->get(['id', 'name']);
    }

    public function getList()
    {
        return DB::table('wh_priority')->get(['id as value', 'name as text']);
    }

    
}