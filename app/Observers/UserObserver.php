<?php

namespace App\Observers;

use App\Models\User;
use App\Repositories\SettingUpdateRepository;
use Illuminate\Support\Facades\DB;

class UserObserver
{
    protected $settingUpdateRepository;
    protected $table = "wh_user_team";

    public function __construct(SettingUpdateRepository $settingUpdateRepository)
    {
        $this->settingUpdateRepository = $settingUpdateRepository;
    }

    public function creating(User $user)
    {
        $defaultpassword = DB::table('wh_organization')->first()->defaultpassword;

        $user->password    = bcrypt($defaultpassword); 
        $user->urlpicture  = env('DEFAULT_AVATAR', 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/dummy.png');
        $user->preferences = '{"theme": "light", "sidebarCollapsed": false}';

        //Asignamos los spots al usuario en creación
        $spots = DB::table('wh_spot')->whereNull('deleted_at')->pluck('id')->toArray();
        $user->spots = json_encode($spots);
    }

    public function created(User $user)
    {
        $this->settingUpdateRepository->register($user);
    }

    public function updated(User $user)
    {
        $this->settingUpdateRepository->register($user);
    }

    public function deleted(User $user)
    {
        $this->settingUpdateRepository->register($user);
    }

    public function restored(User $user)
    {
        //
    }

    public function forceDeleted(User $user)
    {
        //
    }

    //Observer relationships (sync, attached, detached)
    public function belongsToManyAttached($relation, $parent, $ids) 
    {
        $parent->setTable($this->table);
        $this->settingUpdateRepository->register($parent);
    }

    public function belongsToManyDetached($relation, $parent, $ids) 
    {
        $parent->setTable($this->table);
        $this->settingUpdateRepository->register($parent);
    }
}
