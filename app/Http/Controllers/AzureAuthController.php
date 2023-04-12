<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Repositories\AzureAuthRepository;

class AzureAuthController extends Controller
{

    protected $azureAuthrepository;

    public function __construct(AzureAuthRepository $repository)
    {
        $this->azureAuthrepository = $repository;
    }

    public function login365BE(Request $request)
    {
        return $this->azureAuthrepository->login365BE($request);
    }

    public function login365APP(Request $request)
    {
        return $this->azureAuthrepository->login365APP($request);
    }

    public function viewSuccess()
    {
        return view('/pages/azureAuth/success');
    }
}
