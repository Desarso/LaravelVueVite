<?php

namespace App\Http\Controllers\Config;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\SpotTypeRepository;
use App\Repositories\SpotRepository; 
use App\Repositories\OrganizationRepository;
 

use Illuminate\Http\Request;

// Dashboard Configurations
class DashboardController extends Controller
{ 
    protected $organizationRepository;

    public function __construct()
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->organizationRepository = new OrganizationRepository;
    }

    public function index()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=> 'Home'], ['name'=>"Account Configuration"]
        ];
           
        $pageConfigs = ['pageHeader' => true,];
        
        $settings = $this->organizationRepository->planSettings();

        return view('/pages/config/dashboard/index', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'plans'       => $settings
        ]);
    }

    public function stats() {      
        $plan = OrganizationRepository::planSettings();
        $spottypes = 3;

        $result =  array(
                array('name'=> 'spots','used'=> 5, 'max'=> 20),
                array('name'=> 'spottypes','used'=> 3, 'max'=> 5),                 
        );
        return ["success"=>true, "data"=>$result];

    }

    public function notAuthorized()
    {      
        return view("pages.page-not-authorized");
    }
}
