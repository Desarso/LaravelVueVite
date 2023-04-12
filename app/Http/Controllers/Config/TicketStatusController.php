<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\TicketStatusRepository;

class TicketStatusController extends Controller
{
    protected $statusRepository;

    public function __construct(TicketStatusRepository $statusRepository)
    {
        $this->statusRepository   = $statusRepository;
    }

    public function getAllTicketStatusAPP()
    {
        return $this->statusRepository->getListAPP();
    }
}
