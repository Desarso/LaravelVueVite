<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Auth;

class UserDeviceRepository
{
    public function save($request)
    {
        if($request->os == "WEB")
        {
            UserDevice::updateOrCreate(
                ['token' => $request->token, 'os' => $request->os],
                ['iduser' => Auth::id()]
            );
        }
        else
        {
            UserDevice::updateOrCreate(
                ['iduser' => Auth::id(), 'os' => $request->os],
                ['token' => $request->token]
            );
        }

        return response()->json(['success' => true, 'message' => 'Token guardado con éxito']);
    }
}