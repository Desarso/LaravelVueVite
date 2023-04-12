<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\ClockinActivityRepository;

class ClockinActivityController extends Controller
{

    protected $clockinRepository;

    public function __construct(ClockinActivityRepository $clockin)
    {
        $this->clockinRepository = $clockin;
    }


    public function getListApp()
    {
        return $this->clockinRepository->getListApp();
    }
}
