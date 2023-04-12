<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetLoanNote extends Model
{
    protected $table = "wh_asset_loan_note";

    protected $fillable =  ["idassetloan", "note", "created_by", "type"];

    //Relationships
    public function assetLoan()
    {
        return $this->belongsTo(AssetLoan::class, 'idassetloan', 'id');
    }
}
