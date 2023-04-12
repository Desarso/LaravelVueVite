<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\SettingUpdateRepository;

class SettingUpdateController extends Controller
{
    protected $settingUpdateRepository;

    public function __construct(SettingUpdateRepository $settingUpdateRepository)
    {
        $this->settingUpdateRepository = $settingUpdateRepository;
    }

    public function get(Request $request)
    {
        return $this->settingUpdateRepository->get($request);
    }

    public function getListApp()
    {
        return $this->settingUpdateRepository->getListApp();
    }
}
