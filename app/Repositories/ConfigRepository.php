<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;

class ConfigRepository
{
    public function checkRelations($model, $relationships)
    {
        $hasRelation = [];
        foreach ($relationships as $relationship)
        {
            if ($model->$relationship()->count() > 0)
            {
                array_push($hasRelation, $relationship);
            }
        }
        return count($hasRelation) == 0 ? false : $hasRelation;
    }
}