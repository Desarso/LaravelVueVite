<?php

namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserDeviceRepository;

class UserDeviceController extends Controller
{
    protected $userDeviceRepository;

    public function __construct(UserDeviceRepository $userDeviceRepository)
    {
        $this->userDeviceRepository = $userDeviceRepository;
    }

    public function save(Request $request)
    {
        return $this->userDeviceRepository->save($request);
    }
}
