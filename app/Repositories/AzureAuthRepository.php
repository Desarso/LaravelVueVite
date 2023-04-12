<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AzureAuthRepository
{
    public function login365BE($request) {

        $user = $this->registerUser($request);
        Auth::loginUsingId($user->id);

        return redirect(RouteServiceProvider::HOME);
    }
    
    
    public function login365APP(Request $request)
    {
        $user = $this->registerUser($request);

        $url = "azureAuthSuccess?iduser=".$user->id; 
        return redirect($url); 
    }


    public function registerUser(Request $request)
    { 
        $spots =  DB::table('wh_spot')
                    ->pluck('id')
                    ->toArray();

        $firstname = $request->name ?? $request->email;
        $lastname = $request->lastname ?? "";

        $user = User::firstOrCreate(
            [ 'email' => $request->email],
            [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'username' => $request->email,
                'email' => $request->email,
                'spots' => json_encode($spots),
            ]
        );

        return $user;
    }

}