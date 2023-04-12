<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\LogRepository;

class LogController extends Controller
{
    protected $logRepository;

    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    public function getAll(Request $request)
    {
        return $this->logRepository->getAll($request);
    }

    public function getTaskLogsApp(Request $request)
    {
        return $this->logRepository->getTaskLogsApp($request);
    }
    
}
