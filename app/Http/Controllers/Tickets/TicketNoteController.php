<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\TicketNoteRepository;

class TicketNoteController extends Controller
{
    protected $ticketNoteRepository;

    public function __construct(TicketNoteRepository $ticketNoteRepository)
    {
        $this->ticketNoteRepository = $ticketNoteRepository;
    }

    public function getNotes(Request $request)
    {
        return $this->ticketNoteRepository->getNotes($request);
    }

    public function create(Request $request)
    {
        return $this->ticketNoteRepository->create($request);
    }

    public function delete(Request $request)
    {
        return $this->ticketNoteRepository->delete($request);
    }

    
    public function getNotesApp(Request $request)
    {
        return $this->ticketNoteRepository->getNotesApp($request);
    }

    public function deleteNoteApp(Request $request)
    {
        return $this->ticketNoteRepository->deleteNoteApp($request);
    }
}
