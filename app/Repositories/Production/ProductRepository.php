<?php

namespace App\Repositories\Production;
use Illuminate\Support\Facades\DB;
use App\Models\Production\Product;

class ProductRepository
{
    public function getAll()
    {
        return Product::get(["id", "name", "description", "code", "idequipmenttype", "idproductcategory", "idformula", "idpresentation", "iddestination","enabled"]);
    }

    public function getList()
    {
        return Product::where('enabled', true)->get(['id as value', 'name as text']);
    }
    
    public function create($request)
    {
        return Product::create($request->all());
    }

    public function update($request)
    {       
      $model = Product::find($request->id);
      $model->fill($request->all())->save();
      return $model;

    }

    public function delete($request)
    {
        $model = Product::findOrFail($request->id);
        $model->delete();
    }    

}