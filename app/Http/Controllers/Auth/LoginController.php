<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use App\Models\User;
use App\Models\UserTeam;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Session;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }

    // Login
    public function showLoginForm(){
      $pageConfigs = [
          'bodyClass' => "bg-full-screen-image",
          'blankPage' => true
      ];

    //   dd($pageConfigs);

      return view('/auth/login', [
          'pageConfigs' => $pageConfigs
      ]);
    }

    public function authenticated(Request $request, $user)
    {
        if($request->has('timezone'))
        {
            Session::put('local_timezone', $request->timezone);
        }
        else
        {
            Session::put('local_timezone', config('app.timezone'));
        }
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/login');
    }

    public function logoutApp(Request $request)
    {

        UserDevice::where('iduser', $request->iduser)
                    ->where('os', $request->os)
                    ->delete();

        $user = User::find($request->iduser);
        $user->available = 0;
        $user->save();

        return response(['result' => true ]);

    }


    public function loginApp(Request $request)
    {
       
        $result = 0;
        $permissions = '';
        $organization = '';
        $mensage = "¡Usuario Inválido!";

        $user = User::where('username', $request->username)
                    ->where('enabled', 1)
                    ->with('teams:id as idteam,name')
                    ->with('clockin:id as iduser,action')
                    ->get(['id','firstname','lastname','urlpicture', 'chat_uid','username','password', 'available', 'isadmin'])
                    ->first();
        
        if($user)
        {
            
            
            if(Hash::check( $request->password, $user->password))
            {
                $result = 1;
                $mensage = "Exitoso!.";

                if (isset($request->token)) {
                    UserDevice::updateOrCreate(
                        ['iduser' => $user->id, 'os' => $request->os],
                        ['token' => $request->token]
                    );
                }
                
                $permissions = UserTeam::getUserPermissions($user->id);
                $user->timestamps = false;
                $user->version = $request->version;
                $user->available = 1;
                $user->forcelogin = 0;
                $user->last_login_at = Carbon::now();
                $user->save();

                $organization = DB::table('wh_organization')->select('name', 'settings', 'appbar', 'appmenu', 'chat_uid')->first();
                
            } else {
                $mensage = "Contraseña incorrecta!.";
            }
        }

        $result2 = Array(
            'user' => $user,
            'result' => $result,
            'message' => $mensage,
            'permissions' => $permissions,
            'organization' => $organization,
        );

        return response($result2);
    }

    public function loginByIduserApp(Request $request)
    {
       
        $result = 0;
        $permissions = '';
        $organization = '';
        $mensage = "¡Usuario Inválido!";

        $user = User::where('id', $request->iduser)
                    ->with('teams:id as idteam,name')
                    ->with('clockin:id as iduser,action')
                    ->get(['id','firstname','lastname','urlpicture', 'chat_uid','username','password'])
                    ->first();
        
        if($user)
        {
            $result = 1;
            $mensage = "Exitoso!.";

            if (isset($request->token)) {
                UserDevice::updateOrCreate(
                    ['iduser' => $user->id, 'os' => $request->os],
                    ['token' => $request->token]
                );
            }
            
            $permissions = UserTeam::getUserPermissions($user->id);
            $user->timestamps = false;
            $user->version = $request->version;
            $user->available = 1;
            $user->forcelogin = 0;
            $user->last_login_at = Carbon::now();
            $user->save();

            $organization = DB::table('wh_organization')->select('name', 'settings', 'appbar', 'chat_uid')->first();
        }

        $result2 = Array(
            'user' => $user,
            'result' => $result,
            'message' => $mensage,
            'permissions' => $permissions,
            'organization' => $organization,
        );

        return response($result2);
    }



}
