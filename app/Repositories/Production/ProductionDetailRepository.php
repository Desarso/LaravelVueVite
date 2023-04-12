<?php

namespace App\Repositories\Production;


use Illuminate\Support\Facades\DB;
use App\Models\Production\ProductionDetail;
use App\Models\Production\Production;
use App\Models\Production\Product;
use App\Models\Production\Presentation;
use Carbon\Carbon;

class ProductionDetailRepository
{
    public function getAll()
    {
        return ProductionDetail::get( ["id","idproduction", "time", "quantity", "idoperator"]);
    }

    public function getProductionDetails($idproduction) 
    {
        return ProductionDetail::where('idproduction',$idproduction)
        ->orderBy('created_at','desc')->get( ["id","idproduction", "time", "quantity", "idoperator"]); 
    }
   
    
    // Recibe la cantidad en número de cajas
    // Debemos actualizar totalproducido en términos de sobres.

    public function create($request)
    {     
                    
            $request['time'] =  Carbon::now();
            $detail = ProductionDetail::create($request->all());
            // Update Total Produced
            $production = Production::findOrFail($request->idproduction);
            $product = Product::findOrFail($production->idproduct);
            $presentation = Presentation::findOrFail($product->idpresentation);
            $totalproduced = $request->quantity* $presentation->units;

            Production::findOrFail($request->idproduction)->increment('totalproduced', $totalproduced);
            return $detail;
       
        
        
    }

    public function update($request)
    {       
      $model = ProductionDetail::find($request->id);
      $quantity = $model->quantity; // original quantity 
     
      
      $model->fill($request->all())->save();
      
       // Update Total Produced
       $production = Production::findOrFail($request->idproduction);
       $product = Product::findOrFail($production->idproduct);
       $presentation = Presentation::findOrFail($product->idpresentation);
       $totalproduced = $request->quantity* $presentation->units;

       $model->fill($request->all())->save();

      if ($request->quantity > $quantity) // incremented update
        $production->increment('totalproduced', $totalproduced);
    else  
        $production->decrement('totalproduced', $totalproduced);

      return $model;
    }

    public function delete($request)
    {
        $model = ProductionDetail::findOrFail($request->id);

        // Update Total Produced
        $production = Production::findOrFail($request->idproduction);
        $product = Product::findOrFail($production->idproduct);
        $presentation = Presentation::findOrFail($product->idpresentation);
        $totalproduced = $request->quantity* $presentation->units;

        Production::findOrFail($request->idproduction)->decrement('totalproduced',$totalproduced);
        $model->delete();
    }    

}