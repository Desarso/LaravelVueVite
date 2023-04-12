<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;

class MenuRepository
{
    public function getMenu()
    {
        return Menu::with(["submenu" => function ($q) {
                        $q->where('enable', 1);
                   }])
                   ->whereNull('idparent')
                   ->where('enable', 1)
                   ->orderBy('position', 'asc')
                   ->get();
    }

    public function getAll()
    {
        return Menu::with('submenu')->orderBy('position', 'asc')->get();
    }

    public function enable($request)
    {
        $menu = Menu::find($request->id);

        $menu->fill($request->all())->save();

        return response()->json(['success' => true, 'model' => $menu]);
    }
}