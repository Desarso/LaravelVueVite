<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\ClockinActivity;

class ClockinActivityRepository
{

    public function getListApp($withTrashed = false)
    {
       return ClockinActivity::get(['id', 'name']);
    }

}