<?php

namespace App\Repositories\Production;
use Illuminate\Support\Facades\DB;
use App\Models\Production\ProductCategory;

class ProductCategoryRepository
{
    public function getAll()
    {
        return ProductCategory::get( ["name", "description"]);
    }

    public function getList()
    {
        return ProductCategory::get(['id as value', 'name as text']);
    }         

}