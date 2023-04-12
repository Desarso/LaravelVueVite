<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\HolidayRepository;

class HolidayController extends Controller
{
    protected $holidayRepository;

    public function __construct()
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->holidayRepository = new HolidayRepository;
    }

    public function index()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'],['link'=>"/config-dashboard", 'name'=> "Configuration"]
        ];
           
        $pageConfigs = [
            'pageHeader' => true,
            
        ];

        return view('/pages/config/holiday/index', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    public function getAll()
    {
        return $this->holidayRepository->getAll();
    }

    public function getList()
    {
        return $this->holidayRepository->getList();
    }

    public function create(Request $request)
    {
        return $this->holidayRepository->create($request);
    }

    public function update(Request $request)
    {
        return $this->holidayRepository->update($request);
    }

    public function delete(Request $request)
    {
        return $this->holidayRepository->delete($request);
    }
}
