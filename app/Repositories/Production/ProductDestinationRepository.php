<?php

namespace App\Repositories\Production;
use Illuminate\Support\Facades\DB;
use App\Models\Production\ProductDestination;

class ProductDestinationRepository
{
    public function getAll()
    {
        return ProductDestination::get( ["name", "description", "enabled"]);
    }

    public function getList()
    {
        return ProductDestination::where('enabled', true)->get(['id as value', 'name as text']);
    }    

}