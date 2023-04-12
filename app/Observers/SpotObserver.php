<?php

namespace App\Observers;

use App\Models\Spot;
use App\Models\User;
use App\Repositories\SettingUpdateRepository;

class SpotObserver
{
    protected $settingUpdateRepository;

    public function __construct(SettingUpdateRepository $settingUpdateRepository)
    {
        $this->settingUpdateRepository = $settingUpdateRepository;
    }

    public function created(Spot $spot)
    {
        User::unsetEventDispatcher();
        $users = User::whereJsonContains('spots', (int) $spot->idparent)->get(['id', 'spots']);

        $users->each(function ($user, $key) use($spot){
            $json = json_decode($user->spots);
            array_push($json, $spot->id);
            $user->spots = json_encode($json);
            $user->timestamps = false;
            $user->save();
        });

        $this->settingUpdateRepository->register($spot);
    }

    public function updated(Spot $spot)
    {
        $this->settingUpdateRepository->register($spot);
    }

    public function deleting(Spot $spot)
    {
        
    }

    public function deleted(Spot $spot)
    {
        User::unsetEventDispatcher();
        $users = User::whereJsonContains('spots', (int) $spot->id)->get(['id', 'spots']);

        $users->each(function ($user, $key) use($spot){
            $json = json_decode($user->spots);
            $resultado = array_values(array_diff($json, (array)$spot->id));
            $user->spots = json_encode($resultado);
            $user->timestamps = false;
            $user->save();
        });
        
        $this->settingUpdateRepository->register($spot);
    }

    public function restored(Spot $spot)
    {
        //
    }

    public function forceDeleted(Spot $spot)
    {
        //
    }
}
