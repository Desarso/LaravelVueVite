<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserTeam;
use App\Models\Ticket;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class UserRepository
{
    protected $config;

    public function __construct()
    {
        $this->config = new ConfigRepository;
    }

    public function getAll()
    {
        $users = User::with('teams:id as idteam,name,color')->with('roles:id as idrole,name')->withCount('tickets')->get();

        $spotsParents = $this->getParentsSpots()->pluck('value')->toArray();

        $users->each(function ($user, $key) use ($spotsParents) {

            $spots = array_values(array_intersect($spotsParents, json_decode($user->spots)));

            $user->full_teams = join(", ", $user->teams->pluck('name')->all());

            $user->spots = json_encode($spots);

        });

        return $users;
    }

    public function getParentsSpots()
    {
        $parents = DB::table('wh_spot')->select('idparent')->distinct()->get()->pluck('idparent')->toArray();

        return DB::table('wh_spot')->select('id as value', 'name as text', 'idparent')->whereIn('id', $parents)->orWhere('isbranch', true)->get();
    }
       
    public function getList()
    {
        $userBranches = $this->getUserBranch();

        return DB::table('wh_user')
                 ->select('id as value', DB::raw('CONCAT(firstname," ",lastname) AS text'), 'firstname', 'urlpicture', 'enabled', 'spots', 'deleted_at')
                 ->where(function($query) use ($userBranches) {
                    $query->whereJsonContains('wh_user.spots', $userBranches[0]);

                    foreach ($userBranches as $branch)
                    {
                        $query->orWhereJsonContains('wh_user.spots', $branch);      
                    }
                 })
                 ->orderBy('firstname')
                 ->get();
    }

    public function create($request) 
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:wh_user',
        ]);

        if($validator->passes())
        {
            if (!$this->checkLimitUser()) {
                return response()->json(['success' => false, 'errors' => ['¡Ya no tienes licencias!']]);
            }

            $user = User::create($request->all());
            $user->teams()->attach($this->getFormatTeams(json_decode($request->teams)));
            $this->CreateUserChat($user);
            
            return response()->json(['success' => true, 'model' => $user]);
        }
     
        return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
    }

    public function update($request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:wh_user,username,' . $request->id,
        ]);

        if($validator->passes())
        {
            $user = User::find($request->id);

            $user->fill($request->all())->save();
            
            $user->teams()->sync($this->getFormatTeams(json_decode($request->teams)));
    
            return response()->json(['success' => true, 'model' => $user]);
        }
     
        return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
    }

    public function delete($request)
    {
        $user = User::findOrFail($request->id);
        $hasRelations = $this->config->checkRelations($user, ['tickets']);

        if(!$hasRelations)
        {
            $user->delete();
            return response()->json(['success' => true, 'model' => $user]);
        }
        else
        {
            return response()->json(['success' => false, 'model' => $user, 'relations' => $hasRelations]);
        }
    }

    public function getTeams($iduser)
    {
        return DB::table('wh_user_team')
                 ->where('iduser', $iduser)
                 ->pluck('idteam')
                 ->toArray();
    }

    public function getUserSpots($iduser)
    {
        $user = DB::table('wh_user')
                 ->where('id', $iduser)
                 ->first();

        return json_decode($user->spots);
    }

    public function saveSpots($request) 
    {
        $spots = $this->getSpots($request);

        User::where('id', $request->iduser)->update(['spots' => json_encode($spots), 'forcelogin' => true]);

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    }

    private function getSpots($request)
    {
        if(is_null($request->spots)) return [];

        $spots = array_map('intval', $request->spots);

        if(in_array(0, $spots)) 
        {
            return DB::table('wh_spot')->whereNull('deleted_at')->get(['id'])->pluck('id')->toArray();
        }
        else
        {
            $allChildren = [];

            $parents = DB::table('wh_spot')->select('idparent')->distinct()->get()->pluck('idparent')->toArray();

            $data = DB::table('wh_spot')->select('id', 'idparent')->get();

            foreach ($spots as $spot)
            {
                $children = $this->getChildren($spot, $data, $parents);
                
                array_push($allChildren, $children);
            }

            $arraysMerged = array_merge([], ...$allChildren);

            return $arraysMerged;
        }
    }

    public function getChildren($idspot, $models, $parents)
    {
        if(is_null($idspot)) return;

        $result = array();

        $this->getAllChildren($idspot, $models, $parents, $result);

        return $result;
    }

    private function getAllChildren($idspot, $models, $parents, &$result = array())
    {
        array_push($result, $idspot);

        $data = $models->where('idparent', $idspot)->where('id', '!=', $idspot)->whereNotIn('id', $result);

        $withoutChildren = $data->whereNotIn('id', $parents)->pluck('id')->toArray();

        $result = array_merge($result, $withoutChildren);

        $withChildren = $data->whereIn('id', $parents);

        foreach($withChildren as $children)
        {
            $this->getAllChildren($children->id, $models, $result);
        }
    }

    public function saveProfile($request) 
    {
        if(is_null(User::find(Auth::id())->preferences))
        {
            User::where('id', Auth::id())->update(['preferences' => '{"theme": "light", "sidebarCollapsed": false}']);
        }

        User::where('id', Auth::id())->update([
                'username'                      => $request->username,
                'firstname'                     => $request->firstname,
                'lastname'                      => $request->lastname,
                'email'                         => $request->email,
                'preferences->theme'            => $request->theme,
                'preferences->sidebarCollapsed' => ($request->sidebarCollapsed == "true" ? true : false), 
            ]);

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    }

    public function changeDarkMode($request) 
    {
        if(is_null(User::find(Auth::id())->preferences))
        {
            User::where('id', Auth::id())->update(['preferences' => '{"theme": "light", "sidebarCollapsed": false}']);
        }

        User::where('id', Auth::id())->update([
            'preferences->theme' =>$request->theme,
            ]);

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    }

    public function resetPassword($request) 
    {
        $defaultpassword = DB::table('wh_organization')->first()->defaultpassword;

        User::where('id', $request->id)->update(['password' => bcrypt($defaultpassword) ]);

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    }

    public function saveShortcut($request) 
    {
        $user = User::find(Auth::id());

        $shortcuts = (is_null($user->shortcuts) ? [] : json_decode($user->shortcuts));

        if(($key = array_search($request->shortcut, $shortcuts)) !== false)
        {
            unset($shortcuts[$key]);
            $shortcuts = array_values($shortcuts);
        }
        else
        {
            array_push($shortcuts, $request->shortcut);
        }

        $user->shortcuts = json_encode((array)$shortcuts);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    }

    public function savePreferences($request) 
    {
        $user = User::find(Auth::id());

        $preferences = (is_null($user->preferences) ? [] : (array) json_decode($user->preferences));

        $preferences[$request->preference] = ($request->value == "true" ? true : false);

        $user->preferences = json_encode((object)$preferences);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
    }

    public function changePassword($request) 
    {
        $user = Auth::user();

        if(Hash::check($request->currentPassword, $user->password))
        {
            $user->update(['password' => bcrypt($request->password)]);
            return response()->json(['success' => true, 'message' => 'Acción completada con éxito']);
        }
        else
        {
            return response()->json(['success' => false, 'message' => 'La contraseña actual es incorrecta']);
        }
    }

    public function getListApp($updated_at = null, $request)
    {
        $UserBranch = [];
        if ($request->has('iduser')) {
            Auth::loginUsingId($request->iduser);
            $UserBranch = $this->getUserBranch();
        }

        return DB::table('wh_user')
                ->select('id', 'urlpicture', DB::raw('CONCAT(firstname," ",ifNUll(lastname,"")) as fullname'), 'deleted_at')
                ->when(!is_null($updated_at), function ($query) use ($updated_at){
                    return $query->where('updated_at', '>', $updated_at);
                }, function ($query) {
                    return $query->whereNull('deleted_at');
                })
                ->when($request->has('iduser'), function ($query) use ($UserBranch) {
                    $query->where(function($query) use($UserBranch) {
                        $query->whereJsonContains('wh_user.spots', $UserBranch[0]);
    
                        foreach ($UserBranch as $spot) {
                            $query->orWhereJsonContains('wh_user.spots', $spot);      
                        }
                    });
                })
                ->get();
    }

    public function updateAvatarApp($request)
    {

        $user = User::find($request->iduser);
        $urlpicture = '';

        $client = env('DO_SPACES_HOTEL', 'prueba');
        $path   = env('DO_SPACES_ROUTE', 'https://dingdonecdn.nyc3.digitaloceanspaces.com/');
        $name = $client . '/' . uniqid($user->username, false) . ".jpg";

        $upload_success = Storage::disk('spaces')->put($name, base64_decode($request->base64), 'public'); //Guardamos las imaganes en DigitalOcean y obtenemos la ruta de la imagen devuelta por el método "puFile"

        if ($upload_success) $urlpicture = $path . $name;

        $user->urlpicture = $urlpicture;
        $user->save();
        $this->updateUserChatAvatar($user);

        return response()->json([
            "result" => $upload_success,
            "urlpicture" => $urlpicture
        ]);
        
    }

    public function updateUserAPP($request)
    {
        $user = User::find ($request->iduser);
        $user->firstname     = $request->firstname;
        $user->lastname      = $request->lastname;
        $user->username      = $request->username;        
        
        if($request->password != 'null') {
            $user->password = bcrypt($request->password);      
        }
        
        $user->save();
      
        return response()->json([
            "result" => true,
        ]);
    }

    private function getFormatTeams($teams)
    {
        $result = array();
        
        if(is_null($teams)) return [];

        foreach($teams as $team)
        {
            $result[$team->idteam] = ['idrole' => $team->idrole];
        }

        return $result;
    }

    private function getUserTeams($iduser, $teams)
    {
        if(is_null($teams)) return [];

        $result = array();
        $newTeams = array_column($teams, 'value');
        $userTeams = UserTeam::select('idteam', 'idrole')
                             ->where('iduser', $iduser)
                             ->get();

        foreach($userTeams as $team)
        {
            if (in_array($team->idteam, $newTeams) != false) {
                $result[$team->idteam] = ['idrole' => $team->idrole];
            }
        }

        foreach($newTeams as $team)
        {
            if (!property_exists($result, $team)) {
                $result[$team] = ['idrole' => 1];
            }
        }

        return $result;
    }

    
    public function getCleaningUsersAPP()
    {
        return DB::table('wh_user')
                ->select('id', 'urlpicture', DB::raw('CONCAT(firstname," ",ifNUll(lastname,"")) as fullname'))
                ->whereNull('deleted_at')
                ->get();
    }
    
    public function getUserToNotify($ticket)
    {
        $users = DB::table('wh_user_team')
                    ->select('iduser')
                    ->where('idteam', $ticket->idteam)
                    ->pluck('iduser')
                    ->toArray();

        return  DB::table('wh_user')
                    ->select('id')
                    ->whereJsonContains('spots', (int)$ticket->idspot)
                    ->whereIn('id', $users)
                    ->pluck('id')
                    ->toArray();
    }
    
    private function CreateUserChat($user)
    {
        $organization = DB::table('wh_organization')->select('id','name', 'chat_uid')->first();

        if(is_null($organization->chat_uid)) {
            return 0;
        }

        $userChat = DB::table('wh_user')
                    ->select('id AS wh_id', 'urlpicture', DB::raw( "'".$organization->chat_uid . "' AS id_organization") , DB::raw('CONCAT(firstname," ",ifNUll(lastname,"")) as name'))
                    ->where('id', $user->id)
                    ->first();

        $response = Http::post(
            "http://159.89.184.195:3001/api/users/create", [
            'user' => $userChat
        ]);

        if($response->status() == 200)
        {
            $data = json_decode($response->body());
            $chat_uid = $data->user->uid;
            
            DB::table('wh_user')
                    ->where('id', "=", $user->id)
                    ->update(['chat_uid' => $chat_uid]);
        }

    }
    
    private function updateUserChatAvatar($user)
    {
        if (is_null($user->chat_uid)) return null;

        $response = Http::post(
            "http://159.89.184.195:3001/api/users/update-avatar", [
            'uid' => $user->chat_uid,
            'urlpicture' => $user->urlpicture
        ]);

        if($response->status() == 200)
        {
            $data = json_decode($response->body());
        }
    }

    
    public function checkForceloginAPP($request)
    {
        $user = User::find($request->iduser);
        $forcelogin = ($user) ? $user->forcelogin : false;
      
        return response()->json([
            "result" => true,
            "forcelogin" => $forcelogin
        ]);
    }

    public function changePhoto($request) 
    {
        $route = "";
        $client = env('DO_SPACES_CLIENT','dingdone');//obtenemos la variable de entorno con el nombre del cliente
        $path  = env('DO_SPACES_ROUTE','https://dingdonecdn.nyc3.digitaloceanspaces.com/');

        $user = User::find(Auth::id());

        if($request->hasFile('photo'))//validamos que exista la imagen
        {
            $route = Storage::disk('spaces')->putFile($client, $request->file('photo'), 'public'); //Guardamos las imaganes en DigitalOcean y obtenemos la ruta de la imagen devuelta por el método "puFile"
            $route = $path . $route;
            $user->urlpicture = $route;
        }

        $user->save();

        return $user;
    }

    public function restore($request)
    {
        $user = User::withTrashed()->findOrFail($request->id);
        $user->restore();

        return response()->json(['success' => true, 'model' => $user]);
    }

    public function disable($request)
    {
        $user = User::findOrFail($request->id);
        $user->fill($request->all())->save();

        return response()->json(['success' => true, 'model' => $user]);
    }

    public function changeAvailable($request)
    {
        $user = User::findOrFail($request->iduser);
        $user->available = $request->available;
        $user->save();

        return response()->json(['success' => true, 'model' => $user]);
    }

    private function checkLimitUser()
    {   
        $hasLicences = false;
        $organization = DB::table('wh_organization')->select('id','name', 'chat_uid')->first();

        $response = Http::post(
            "https://webadmin.whagons.com/api/getLimitUsersByclient", 
            ['name' => $organization->name]
        );

        if($response->status() == 200)
        {
            $data = json_decode($response->body());
            
            
            if($data->success) {
                $userActive = $this->getUserCount();
                $hasLicences = $userActive < $data->user_limit;
            }
        }

        return $hasLicences;
    }

    public function getUserCount()
    {
        $userList = User::where('issuperadmin', 0)->get();
        return $userList->count();
    }

    public function setUserLimit($request)
    {
        $organization = DB::table('wh_organization')->select('plansettings')->first();
        $settigins = json_decode($organization->plansettings);
        $settigins = collect($settigins);

        foreach ($settigins as $item ) {
            if ($item->name == 'Users') {
                $item->maxvalue = $request->limit;
            }
        }

        DB::table('wh_organization')
                    ->where('id', 1)
                    ->update(['plansettings' => json_encode($settigins)]);

        return response()->json(['success' => true]);
    }

    public function getUserBranch()
    {
        $userSpots = $this->getUserSpots(Auth::id());
        return DB::table('wh_spot')
                    ->select('id')
                    ->whereNull('deleted_at')
                    ->where('isbranch', 1)
                    ->whereIn('id', $userSpots)
                    ->get()
                    ->pluck('id')
                    ->toArray();
    }
}