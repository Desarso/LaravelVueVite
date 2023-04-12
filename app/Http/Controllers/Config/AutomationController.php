<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AutomationController extends Controller
{
    // Dashboard - Analytics
    public function index(){
        $pageConfigs = [
            'pageHeader' => false
        ];



        return view('/pages/config/automation/index', [
            'pageConfigs' => $pageConfigs,
             
        ]);
    }

  
}

