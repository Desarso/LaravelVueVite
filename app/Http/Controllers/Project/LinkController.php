<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Project\LinkRepository;
 
 

class LinkController extends Controller
{
    protected $link;

    public function __construct(LinkRepository $link)
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->link = $link;
    }

   

    public function store(Request $request)
    {
        return $this->link->store($request);        
    }
 
    public function update($id, Request $request)
    {
        return $this->link->update($id, $request);     
    }
 
    public function destroy($id)
    {
        return $this->link->destroy($id);        
    }
   
   
 


 
    
}
