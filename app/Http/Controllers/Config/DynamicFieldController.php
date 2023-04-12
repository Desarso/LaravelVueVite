<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\DynamicFieldRepository;

class DynamicFieldController extends Controller
{
    protected $DynamicField;

    public function __construct(DynamicFieldRepository $dynamicField)
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->dynamicField = $dynamicField;
    }

    public function index()
    {        
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard",'name'=>"Configuration"], ['name'=>"Dynamic Fields"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,
            
        ];

        return view('/pages/config/dynamicfields/index', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    public function getAll()
    {
        return $this->DynamicField->getAll();
    }

    public function getList()
    {
        return $this->DynamicField->getList();
    }

    public function create(Request $request)
    {
        return $this->DynamicField->create($request);
    }

    public function update(Request $request)
    {
        return $this->DynamicField->update($request);
    }

    public function delete(Request $request)
    {
        return $this->DynamicField->delete($request);
    }
}
