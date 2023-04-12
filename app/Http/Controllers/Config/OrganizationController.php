<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\OrganizationRepository;

class OrganizationController extends Controller
{
    public function __construct(OrganizationRepository $org)
    {
        $this->middleware('auth', ['only' => 'index']);
        $this->org = $org;
    }
    
    public function planSettings()
    {
       return json_encode($this->org->planSettings());
    }

}
