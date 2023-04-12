<?php

namespace App\Observers;

use App\Models\TicketNote;
use App\Repositories\LogRepository;
use App\Enums\LogAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TicketNoteObserver
{
    protected $logRepository;

    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    public function created(TicketNote $ticketNote)
    {
        $idUser = is_null(Auth::id()) ? Session::get('iduser') : Auth::id();
        $this->logRepository->register(LogAction::CreateNote, null, $idUser, $ticketNote);
    }

    public function updated(TicketNote $ticketNote)
    {
        //
    }

    public function deleted(TicketNote $ticketNote)
    {
        $idUser = is_null(Auth::id()) ? Session::get('iduser') : Auth::id();
        $this->logRepository->register(LogAction::DeleteNote, null, $idUser, $ticketNote);
    }

    public function restored(TicketNote $ticketNote)
    {
        //
    }

    public function forceDeleted(TicketNote $ticketNote)
    {
        //
    }
}
