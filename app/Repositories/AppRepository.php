<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\App;
use App\Helpers\Helper;

class AppRepository
{
    public function getAll()
    {
        return App::get(['id', 'name', 'description', 'icon', 'color', 'position', 'url', 'enabled']);
    }

    
    public function getAllAPP()
    {
        $items = App::orderBy('enabled', 'DESC')
                    ->orderBy('position', 'ASC')
                    ->get(['id', 'name', 'description', 'icon', 'color', 'position', 'route_app', 'enabled']);
    
        $items->map(function ($item) {
            $item->icon = helper::formatIcon($item->icon);
        });

        return $items;
    }

}