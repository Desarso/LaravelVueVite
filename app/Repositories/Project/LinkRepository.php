<?php

namespace App\Repositories\Project;

use Illuminate\Support\Facades\DB; 
 
use App\Models\Link;
use Session;
use Carbon\Carbon;

class LinkRepository
{
    
    public function store($request){
        $link = new Link();
 
        $link->type = $request->type;
        $link->source = $request->source;
        $link->target = $request->target;
 
        $link->save();
 
        return response()->json([
            "action"=> "inserted",
            "tid" => $link->id
        ]);
    }
 
    public function update($id, $request){
        $link = Link::find($id);
 
        $link->type = $request->type;
        $link->source = $request->source;
        $link->target = $request->target;
 
        $link->save();
 
        return response()->json([
            "action"=> "updated"
        ]);
    }
 
    public function destroy($id){
        $link = Link::find($id);
        $link->delete();
 
        return response()->json([
            "action"=> "deleted"
        ]);
    }
 

    
}
