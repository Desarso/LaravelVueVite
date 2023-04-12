<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ConfigRepository;

class RoleRepository
{
    protected $config;

    public function __construct()
    {
        $this->config = new ConfigRepository;
    }

    public function getAll()
    {
        return Role::orderBy('name', 'asc')->get();
    }

    public function getList()
    {
        return DB::table('wh_role')->get(['id as value', 'name as text']);
    }

    public function create($request)
    {
        $role = Role::create($request->all());

        return response()->json(['success' => true, 'model' => $role]);
    }

    public function update($request)
    {
        $role = Role::find($request->id);
        $role->fill($request->all())->save();

        return response()->json(['success' => true, 'model' => $role]);
    }

    public function delete($request)
    {
        $role = Role::findOrFail($request->id);
        $hasRelations = $this->config->checkRelations($role, ['users']);

        if(!$hasRelations)
        {
            $role->delete();
            return response()->json(['success' => true, 'model' => $role]);
        }
        else
        {
            return response()->json(['success' => false, 'model' => $role, 'relations' => $hasRelations]);
        }
    }

    public function restore($request)
    {
        $role = Role::withTrashed()->findOrFail($request->id);
        $role->restore();

        return response()->json(['success' => true, 'model' => $role]);
    }

    public function getPermissions()
    {
        return DB::table('wh_user_team as rt')
                 ->join('wh_role as r', 'r.id', '=', 'rt.idrole')
                 ->where('rt.iduser', Auth::id())
                 ->select('rt.idteam', 'r.permissions')
                 ->get();
    }

    public function change($request)
    {
        $value = ($request->value == 'false') ? false : true;

        Role::where('id', $request->idrole)->update(['permissions->' . $request->permission => $value]);
        Role::fireModelEvent('updated');
    }
}