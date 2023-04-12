<?php

namespace App\Observers;

use App\Models\Role;
use App\Repositories\SettingUpdateRepository;

class RoleObserver
{
    protected $settingUpdateRepository;
    protected $table = "wh_user_team";

    public function __construct(SettingUpdateRepository $settingUpdateRepository)
    {
        $this->settingUpdateRepository = $settingUpdateRepository;
    }

    public function created(Role $role)
    {
        $role->setTable($this->table);
        $this->settingUpdateRepository->register($role);
    }

    public function updated(Role $role)
    {
        $role->setTable($this->table);
        $this->settingUpdateRepository->register($role);
    }

    public function deleted(Role $role)
    {
        $role->setTable($this->table);
        $this->settingUpdateRepository->register($role);
    }

    public function restored(Role $role)
    {
        //
    }

    public function forceDeleted(Role $role)
    {
        //
    }
}
