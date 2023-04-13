<?php

namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserStatsAppController extends Controller
{
    // User Settings App
    public function user_stats(){
      $breadcrumbs = [
        ['link'=>"dashboard-analytics",'name'=>"Home"],['link'=>"dashboard-analytics",'name'=>"Pages"], ['name'=>"User Settings"]
      ];

      return view('/pages/app-user-stats', [
        'breadcrumbs' => $breadcrumbs
      ]);
    }
}
