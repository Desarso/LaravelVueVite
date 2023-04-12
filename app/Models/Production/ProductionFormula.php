<?php

namespace App\Models\Production;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionFormula extends Model
{
    use SoftDeletes;
    protected $table = "wh_production_formula";
    protected $fillable =  ["name", "description", "inputs",];

    
   

}
