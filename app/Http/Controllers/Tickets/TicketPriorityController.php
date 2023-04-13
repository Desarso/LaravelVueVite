<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Repositories\TicketPriorityRepository;

class TicketPriorityController extends Controller
{
    protected $priority;

    public function __construct(TicketPriorityRepository $priority)
    {
        $this->priority = $priority;
    }

    public function getListApp()
    {
        return $this->priority->getListApp();
    }
}