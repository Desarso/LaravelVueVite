<?php

namespace App\Repositories\Cleaning;
use Illuminate\Support\Facades\DB;
use App\Models\Cleaning\CleaningStatus;
use Helper;


class CleaningStatusRepository
{
    public function getAll()
    {
        return DB::table('wh_cleaning_status')->get(['id', 'name', 'color', 'background', 'icon']);
    }

    public function getList()
    {
        return DB::table('wh_cleaning_status')->get(['id as value', 'name as text', 'background']);
    }

    public function getAllAPP()
    {
        $items = DB::table('wh_cleaning_status')->get(['id', 'name', 'color', 'background', 'icon']);
    
        $items->map(function ($item) {
            $item->icon = helper::formatIcon($item->icon);
            return $item;
        });

        return $items;
    }
}