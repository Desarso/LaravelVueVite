<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\ProtocolType;

class ProtocolTypeRepository
{
    public function getAll()
    {
        return ProtocolType::whereNull('deleted_at')->get(["id","name", "description"]);
    }

    public function getList()
    {
        return DB::table('wh_protocol_type')->get(['id as value', 'name as text']);
    }
  
   
}